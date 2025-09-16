<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['teacher_id'])){
    exit("Not authorized");
}

$teacher_id = $_SESSION['teacher_id'];

if(isset($_POST['class_id'])){
    $class_id = intval($_POST['class_id']);

    // ✅ Verify teacher has access to this class
    $check = $conn->query("SELECT * FROM teacher_class WHERE teacher_id='$teacher_id' AND class_id='$class_id'");
    if($check->num_rows == 0){
        echo '<div class="col-12 text-danger">You are not assigned to this class.</div>';
        exit;
    }

    // ✅ Fetch students in this class
    $students = $conn->query("SELECT * FROM students WHERE class_id = '$class_id'");
    
    if($students->num_rows > 0){
        while($row = $students->fetch_assoc()){
            echo '<div class="col-md-4">
                    <div class="student-card">
                        <h6>'.htmlspecialchars($row['firstname'].' '.$row['lastname']).'</h6>
                        <p class="mb-0 text-muted">ID: '.$row['id'].'</p>
                    </div>
                  </div>';
        }
    } else {
        echo '<div class="col-12 text-muted">No students found for this class.</div>';
    }
}
?>
