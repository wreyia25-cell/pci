<?php
ob_start();
date_default_timezone_set("Asia/Manila");
include 'db_connect.php';   // <--- ADD THIS AT THE VERY TOP
$action = isset($_GET['action']) ? $_GET['action'] : '';
include 'admin_class.php';
$crud = new Action();

// ----------------------- Authentication -----------------------
if ($action === 'login') {
    echo $crud->login();
}
if ($action === 'teacherLogin') {
    echo $crud->loginTeacher();
}
if ($action === 'login2') {
    echo $crud->login2();
}
if ($action === 'logout') {
    echo $crud->logout();
}
if ($action === 'logout2') {
    echo $crud->logout2();
}

// ----------------------- User Management -----------------------
if ($action === 'signup') {
    echo $crud->signup();
}
if ($action === 'save_user') {
    echo $crud->save_user();
}
if ($action === 'update_user') {
    echo $crud->update_user();
}
if ($action === 'delete_user') {
    echo $crud->delete_user();
}
if(isset($_POST['id']) && isset($_POST['student_id'])){
    $id = intval($_POST['id']);
    $student_id = intval($_POST['student_id']);
    $note = $conn->real_escape_string($_POST['note']);
    $isActive = ($_POST['isActive'] == 1) ? 1 : 0;

    $errors = [];

    $update1 = $conn->query("UPDATE results SET note = '$note' WHERE id = $id");
    if(!$update1){
        $errors[] = "Results update error: ".$conn->error;
    }

    $update2 = $conn->query("UPDATE students SET isActive = $isActive WHERE id = $student_id");
    if(!$update2){
        $errors[] = "Students update error: ".$conn->error;
    }

    if(empty($errors)){
        echo json_encode(['status'=>1]);
    } else {
        echo json_encode(['status'=>0, 'errors'=>$errors]);
    }
}


// ----------------------- Class Management -----------------------
if ($action === 'save_class') {
    echo $crud->save_class();
}
if ($action === 'delete_class') {
    echo $crud->delete_class();
}

// ----------------------- Subject Management -----------------------
if ($action === 'save_subject') {
    echo $crud->save_subject();
}
if ($action === 'delete_subject') {
    echo $crud->delete_subject();
}

// ----------------------- Student Management -----------------------
if ($action === 'save_student') {
    echo $crud->save_student();
}
if ($action === 'delete_student') {
    echo $crud->delete_student();
}

// ----------------------- Result Management -----------------------
if ($action === 'save_result') {
    echo $crud->save_result();
}
if ($action === 'delete_result') {
    echo $crud->delete_result();
}

// ----------------------- Get Students by Level & Section -----------------------
if ($action === 'get_students_by_class') {
    if(!isset($conn)) include 'db_connect.php';

    $level = isset($_POST['level']) ? $conn->real_escape_string($_POST['level']) : '';
    $section = isset($_POST['section']) ? $conn->real_escape_string($_POST['section']) : '';

    if($level !== '' && $section !== ''){
        $class_query = $conn->query("SELECT id FROM classes WHERE level='$level' AND section='$section'");
        $class_ids = [];
        while($row = $class_query->fetch_assoc()) $class_ids[] = $row['id'];

        if(count($class_ids) > 0){
            $ids = implode(',', $class_ids);
            $student_query = $conn->query("
                SELECT s.id, s.student_code, CONCAT(s.firstname,' ',s.middlename,' ',s.lastname) AS name, 
                       c.id AS class_id, CONCAT(c.level,'-',c.section) AS class
                FROM students s
                INNER JOIN classes c ON s.class_id = c.id
                WHERE s.class_id IN ($ids)
                ORDER BY s.firstname, s.middlename, s.lastname ASC
            ");

            echo '<option value="">Select Student</option>';
            while($s = $student_query->fetch_assoc()){
                echo "<option value='{$s['id']}' data-class_id='{$s['class_id']}' data-class='{$s['class']}'>
                        {$s['student_code']} | ".ucwords($s['name'])."
                      </option>";
            }
        } else {
            echo '<option value="">No students found</option>';
        }
    } else {
        echo '<option value="">Select Level & Section first</option>';
    }
    exit;
}
if($_GET['action'] == 'save_teacher'){
    if(!isset($conn)) include 'db_connect.php';

    // Sanitize inputs
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name  = $conn->real_escape_string($_POST['last_name']);
    $email      = $conn->real_escape_string($_POST['email']);
    $phone      = $conn->real_escape_string($_POST['phone_number']);
    $hire_date  = !empty($_POST['hire_date']) ? $conn->real_escape_string($_POST['hire_date']) : NULL;
    $qualification = $conn->real_escape_string($_POST['qualification']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash the password

    $conn->begin_transaction();
    try {
        // 1. Insert teacher with password
        $sql = "INSERT INTO teachers (first_name, last_name, email, password, phone_number, hire_date, qualification)
                VALUES ('$first_name','$last_name','$email','$password','$phone', ".($hire_date ? "'$hire_date'" : "NULL").", '$qualification')";
        if(!$conn->query($sql)) throw new Exception($conn->error);
        $teacher_id = $conn->insert_id;

        // 2. Insert teacher_subject
        if(isset($_POST['subject_id']) && is_array($_POST['subject_id'])){
            foreach($_POST['subject_id'] as $subject_id){
                $subject_id = $conn->real_escape_string($subject_id);
                if(!$conn->query("INSERT INTO teacher_subject (teacher_id, subject_id) VALUES ('$teacher_id','$subject_id')"))
                    throw new Exception($conn->error);
            }
        }

        // 3. Insert teacher_class
        if(isset($_POST['class_id']) && is_array($_POST['class_id'])){
            foreach($_POST['class_id'] as $class_id){
                $class_id = $conn->real_escape_string($class_id);
                if(!$conn->query("INSERT INTO teacher_class (teacher_id, class_id) VALUES ('$teacher_id','$class_id')"))
                    throw new Exception($conn->error);
            }
        }

        $conn->commit();
        echo 1; // success
    } catch(Exception $e){
        $conn->rollback();
        echo "Error saving data: ".$e->getMessage();
    }
}



ob_end_flush();
?>
