<?php
include('./admin/database_connection.php');
session_start();

if (isset($_POST['grade_id']) && $_POST['grade_id'] != '') {
  $query = "
    SELECT student_id, student_name, student_roll_number 
    FROM tbl_student 
    WHERE student_grade_id = :grade_id
    ORDER BY student_name ASC
  ";
  $statement = $connect->prepare($query);
  $statement->execute([':grade_id' => $_POST['grade_id']]);
  $students = $statement->fetchAll();

  if(count($students) > 0) {
    echo '<table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>Roll No.</th>
          <th>Student Name</th>
          <th>Present</th>
          <th>Absent</th>
          <th>Leave</th>
        </tr>
      </thead>
      <tbody>';
    foreach($students as $student) {
      echo '<tr>
        <td>'.htmlspecialchars($student["student_roll_number"]).'</td>
        <td>'.htmlspecialchars($student["student_name"]).'
          <input type="hidden" name="student_id[]" value="'.htmlspecialchars($student["student_id"]).'" />
        </td>
        <td><input type="radio" name="attendance_status'.$student["student_id"].'" value="Present" /></td>
        <td><input type="radio" name="attendance_status'.$student["student_id"].'" checked value="Absent" /></td>
        <td><input type="radio" name="attendance_status'.$student["student_id"].'" value="Leave" /></td>
      </tr>';
    }
    echo '</tbody></table>';
  } else {
    echo '<div class="alert alert-warning">No students found for this grade.</div>';
  }
}
?>
