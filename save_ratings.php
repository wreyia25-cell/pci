<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['teacher_id'])) exit("Not authorized");

$teacher_id = $_SESSION['teacher_id'];

if (isset($_POST['rating'], $_POST['class_id'], $_POST['subject_id'])) {
    $class_id = intval($_POST['class_id']);
    $subject_id = intval($_POST['subject_id']);
    $week_number = date('W');
    $today = date('Y-m-d');

    foreach ($_POST['rating'] as $student_id => $rating_status) {
        $student_id = intval($student_id);
        $rating_status = $conn->real_escape_string($rating_status);
        $attendance_status = isset($_POST['attendance'][$student_id]) ? $conn->real_escape_string($_POST['attendance'][$student_id]) : 'Absent';

        // Insert into results
        $insertResult = $conn->query("
            INSERT INTO results (student_id, class_id, date_created, week_number, note)
            VALUES ('$student_id', '$class_id', '$today', '$week_number', '')
        ");
        if(!$insertResult){
            echo "Error inserting result for student $student_id: ".$conn->error;
            continue;
        }

        $result_id = $conn->insert_id;

        // Insert into result_items
        $insertItem = $conn->query("
            INSERT INTO result_items (result_id, subject_id, stautus, date_created, attendance_status)
            VALUES ('$result_id', '$subject_id', '$rating_status', '$today', '$attendance_status')
        ");
        if(!$insertItem){
            echo "Error inserting result_item for student $student_id: ".$conn->error;
        }
    }

    echo "✅ Ratings and attendance saved successfully!";
} else {
    echo "❌ No data received.";
}
?>
