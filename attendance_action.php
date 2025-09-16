<?php

// attendance_action.php

include('admin/database_connection.php');

session_start();

if(isset($_POST["action"]))
{
    $teacher_id = $_SESSION["teacher_id"];

    if($_POST["action"] == "fetch")
    {
        $query = "
     SELECT * FROM tbl_attendance a
INNER JOIN tbl_student s ON s.student_id = a.student_id
INNER JOIN tbl_grade g ON g.grade_id = s.student_grade_id
WHERE a.teacher_id = :teacher_id
AND s.student_grade_id IN (
    SELECT grade_id FROM tbl_teacher_grade WHERE teacher_id = :teacher_id
)
AND ( 
        ";

        if(isset($_POST["search"]["value"]))
        {
            $query .= '
            s.student_name LIKE :search_value
            OR s.student_roll_number LIKE :search_value
            OR a.attendance_status LIKE :search_value
            OR a.attendance_date LIKE :search_value) 
            ';
        }
        else
        {
            $query .= '1=1) ';
        }

        if(isset($_POST["order"]))
        {
            $columns = [
                0 => 's.student_name',
                1 => 's.student_roll_number',
                2 => 'g.grade_name',
                3 => 'a.attendance_status',
                4 => 'a.attendance_date'
            ];

            $order_column = $columns[$_POST['order']['0']['column']] ?? 'a.attendance_id';
            $order_dir = ($_POST['order']['0']['dir'] === 'asc') ? 'ASC' : 'DESC';

            $query .= " ORDER BY $order_column $order_dir ";
        }
        else
        {
            $query .= ' ORDER BY a.attendance_id DESC ';
        }

        if($_POST["length"] != -1)
        {
            $start = intval($_POST['start']);
            $length = intval($_POST['length']);
            $query .= " LIMIT $start, $length";
        }

        $statement = $connect->prepare($query);

        if(isset($_POST["search"]["value"]))
        {
            $search_param = '%' . $_POST["search"]["value"] . '%';
            $statement->bindParam(':search_value', $search_param, PDO::PARAM_STR);
        }

        $statement->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll();
        $filtered_rows = $statement->rowCount();
        $data = [];

        foreach($result as $row)
        {
            $status = '';
            if($row["attendance_status"] == "Present")
                $status = '<label class="badge badge-success">Present</label>';
            elseif($row["attendance_status"] == "Absent")
                $status = '<label class="badge badge-danger">Absent</label>';
            elseif($row["attendance_status"] == "Leave")
                $status = '<label class="badge badge-info">Leave</label>';

            $data[] = [
                $row["student_name"],
                $row["student_roll_number"],
                $row["grade_name"],
                $status,
                $row["attendance_date"]
            ];
        }

        $output = [
            'draw' => intval($_POST["draw"]),
            'recordsTotal' => $filtered_rows,
            'recordsFiltered' => get_total_records($connect, 'tbl_attendance'),
            'data' => $data
        ];

        echo json_encode($output);
    }

    if($_POST["action"] == "Add")
    {
        $attendance_date = $_POST["attendance_date"] ?? '';
        $grade_id = $_POST["grade_id"] ?? '';

        $error = false;
        $error_message = '';

        if(empty($attendance_date))
        {
            $error = true;
            $error_message = 'Attendance Date is required';
        }
        else if(empty($grade_id))
        {
            $error = true;
            $error_message = 'Grade is required';
        }

        if($error)
        {
            echo json_encode(['error' => true, 'error_attendance_date' => $error_message]);
            exit;
        }

        // Check if attendance already exists for this teacher, date, and grade
        $query = "
            SELECT 1 FROM tbl_attendance a
            INNER JOIN tbl_student s ON a.student_id = s.student_id
            WHERE a.teacher_id = :teacher_id
            AND a.attendance_date = :attendance_date
            AND s.student_grade_id = :grade_id
            LIMIT 1
        ";

        $statement = $connect->prepare($query);
        $statement->execute([
            ':teacher_id' => $teacher_id,
            ':attendance_date' => $attendance_date,
            ':grade_id' => $grade_id
        ]);

        if($statement->rowCount() > 0)
        {
            echo json_encode([
                'error' => true,
                'error_attendance_date' => 'Attendance Data Already Exists on this date for the selected grade'
            ]);
            exit;
        }

        // Insert attendance data
        $student_ids = $_POST["student_id"] ?? [];
        foreach($student_ids as $student_id)
        {
            $attendance_status = $_POST["attendance_status" . $student_id] ?? 'Absent';

            $insert_query = "
                INSERT INTO tbl_attendance (student_id, attendance_status, attendance_date, teacher_id)
                VALUES (:student_id, :attendance_status, :attendance_date, :teacher_id)
            ";
            $statement = $connect->prepare($insert_query);
            $statement->execute([
                ':student_id' => $student_id,
                ':attendance_status' => $attendance_status,
                ':attendance_date' => $attendance_date,
                ':teacher_id' => $teacher_id
            ]);
        }

        echo json_encode(['success' => 'Data Added Successfully']);
    }

    if($_POST["action"] == "index_fetch")
    {
        $query = "
        SELECT s.*, g.grade_name FROM tbl_student s
        INNER JOIN tbl_grade g ON g.grade_id = s.student_grade_id
        WHERE s.student_grade_id IN (
            SELECT grade_id FROM tbl_teacher_grade WHERE teacher_id = :teacher_id
        )
        ";

        if(isset($_POST["search"]["value"]))
        {
            $query .= '
            AND (
                s.student_name LIKE :search_value
                OR s.student_roll_number LIKE :search_value
                OR g.grade_name LIKE :search_value
            )
            ';
        }

        $query .= ' GROUP BY s.student_id ';

        if(isset($_POST["order"]))
        {
            $columns = [
                0 => 's.student_name',
                1 => 's.student_roll_number',
                2 => 'g.grade_name',
            ];

            $order_column = $columns[$_POST['order']['0']['column']] ?? 's.student_roll_number';
            $order_dir = ($_POST['order']['0']['dir'] === 'asc') ? 'ASC' : 'DESC';

            $query .= " ORDER BY $order_column $order_dir ";
        }
        else
        {
            $query .= ' ORDER BY s.student_roll_number ASC ';
        }

        if($_POST["length"] != -1)
        {
            $start = intval($_POST['start']);
            $length = intval($_POST['length']);
            $query .= " LIMIT $start, $length";
        }

        $statement = $connect->prepare($query);

        if(isset($_POST["search"]["value"]))
        {
            $search_param = '%' . $_POST["search"]["value"] . '%';
            $statement->bindParam(':search_value', $search_param, PDO::PARAM_STR);
        }

        $statement->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetchAll();
        $filtered_rows = $statement->rowCount();
        $data = [];

        foreach($result as $row)
        {
            $data[] = [
                $row["student_name"],
                $row["student_roll_number"],
                $row["grade_name"],
                get_attendance_percentage($connect, $row["student_id"]),
                '<button type="button" name="report_button" id="'.$row["student_id"].'" class="btn btn-info btn-sm report_button">Report</button>'
            ];
        }

        $output = [
            'draw' => intval($_POST["draw"]),
            'recordsTotal' => $filtered_rows,
            'recordsFiltered' => get_total_records($connect, 'tbl_student'),
            'data' => $data
        ];

        echo json_encode($output);
    }
}

?>
