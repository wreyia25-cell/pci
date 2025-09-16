<?php
session_start();

// Destroy all session variables related to teacher
foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'teacher_') === 0) {
        unset($_SESSION[$key]);
    }
}

// Destroy the session
session_destroy();

// Redirect to teacher login page
header("Location: teacher_login.php");
exit();
?>