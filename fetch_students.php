<?php
session_start();
include 'db_connect.php';

if(!isset($_SESSION['teacher_id'])) exit("Not authorized");

$teacher_id = $_SESSION['teacher_id'];

if(!isset($_POST['class_id'])) exit("Class ID not provided.");

$class_id = intval($_POST['class_id']);

// Verify teacher access
$check = $conn->query("SELECT * FROM teacher_class WHERE teacher_id='$teacher_id' AND class_id='$class_id'");
if($check->num_rows == 0){
    echo '<tr><td colspan="7" class="text-danger text-center">You are not assigned to this class.</td></tr>';
    exit;
}

// Fetch students
$students = $conn->query("SELECT id, firstname, middlename, lastname FROM students WHERE class_id='$class_id' ORDER BY firstname");
if($students->num_rows > 0){
    while($row = $students->fetch_assoc()){
        $student_id = $row['id'];
        echo "<tr>
                <td>
                    <div class='d-flex align-items-center'>
                        <div class='student-avatar'>".strtoupper($row['firstname'][0])."</div>
                        ".htmlspecialchars($row['firstname'].' '.$row['middlename'].' '.$row['lastname'])."
                    </div>
                </td>
                <td class='text-center'><input type='radio' name='rating[$student_id]' value='Excellent'></td>
                <td class='text-center'><input type='radio' name='rating[$student_id]' value='Very Good'></td>
                <td class='text-center'><input type='radio' name='rating[$student_id]' value='Good'></td>
                <td class='text-center'><input type='radio' name='rating[$student_id]' value='Not Good'></td>
                <td class='text-center'><input type='radio' name='attendance[$student_id]' value='Present' checked></td>
                <td class='text-center'><input type='radio' name='attendance[$student_id]' value='Absent'></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center text-muted'>No students found</td></tr>";
}
?>
