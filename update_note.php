<?php
include 'db_connect.php';
session_start();

if(isset($_POST['student_id']) && isset($_POST['isActive'])){
    $id = $_POST['student_id'];
    $isActive = $_POST['isActive'];

    $stmt = $conn->prepare("UPDATE students SET isActive = ? WHERE id = ?");
    $stmt->bind_param("ii", $isActive, $id);

    if($stmt->execute()){
        echo "1";
    } else {
        echo "0";
    }
    $stmt->close();
}
$conn->close();
?>
