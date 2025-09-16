<?php if(!isset($conn)){ include 'db_connect.php'; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Portal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    body {
      height: 100vh;
      margin: 0;
      background: url('assets/brayan-icon.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
      position: relative;
    }
    body::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.55);
    }
    .login-card {
      position: relative;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.4);
      padding: 35px;
      color: #fff;
      width: 100%;
      max-width: 430px;
      text-align: center;
      animation: fadeInUp 0.7s ease;
      z-index: 2;
    }
    .school-logo {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      margin-bottom: 10px;
      border: 3px solid rgba(255,255,255,0.4);
      background: rgba(255,255,255,0.1);
      padding: 6px;
    }
    .school-name {
      font-size: 1.5rem;
      font-weight: bold;
      letter-spacing: 1px;
      margin-bottom: 5px;
      color: #00f2fe;
      text-shadow: 0 2px 8px rgba(0,0,0,0.7);
    }
    .login-card h4 {
      margin-bottom: 25px;
      font-weight: 600;
    }
    .form-group {
      position: relative;
      margin-bottom: 20px;
    }
    .form-group i {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #bbb;
    }
    .form-control {
      border-radius: 10px;
      padding: 12px 40px;
      background-color: rgba(255,255,255,0.15);
      border: none;
      color: #fff;
    }
    .form-control:focus {
      background-color: rgba(255,255,255,0.25);
      box-shadow: 0 0 0 2px #4facfe;
      color: #fff;
    }
    .btn-gradient {
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-weight: bold;
      padding: 12px;
      transition: all 0.3s ease;
    }
    .btn-gradient:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    }
    .btn-secondary {
      border-radius: 10px;
    }
    .alert {
      margin-bottom: 15px;
    }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #bbb;
    }
    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    @media(max-width: 480px){
      .login-card { padding: 25px; }
      .form-control { font-size: 14px; }
    }
  </style>
</head>
<body>

<div class="login-card">
  <!-- شعار واسم المدرسة -->
  <img src="assets/school-logo.png" alt="School Logo" class="school-logo">
  <div class="school-name">My School Name</div>

  <h4>Welcome</h4>

  <!-- First screen -->
  <div id="login-options">
    <button id="teacher-btn" class="btn btn-gradient btn-block"><i class="fa fa-chalkboard-teacher"></i> Teacher Login</button>
    <button id="student-btn" class="btn btn-gradient btn-block"><i class="fa fa-user-graduate"></i> Student Login</button>
  </div>

  <!-- Teacher form -->
  <form id="teacher-frm" style="display:none;">
    <div id="alert-msg-teacher"></div>
    <div class="form-group text-left">
      <i class="fa fa-envelope"></i>
      <input type="email" name="email" class="form-control" placeholder="Enter email" required>
    </div>
    <div class="form-group text-left">
      <i class="fa fa-lock"></i>
      <input type="password" name="password" id="teacher-password" class="form-control" placeholder="Enter password" required>
      <i class="fa fa-eye toggle-password" toggle="#teacher-password"></i>
    </div>
    <button type="submit" class="btn btn-gradient btn-block">Login</button>
    <button type="button" id="back-btn1" class="btn btn-secondary btn-block">Back</button>
  </form>

  <!-- Student form -->
  <form id="student-frm" style="display:none;">
    <div id="alert-msg-student"></div>
    <div class="form-group text-left">
      <i class="fa fa-id-card"></i>
      <input type="text" name="student_code" class="form-control" placeholder="Enter your student code" required>
    </div>
    <button type="submit" class="btn btn-gradient btn-block">Login</button>
    <button type="button" id="back-btn2" class="btn btn-secondary btn-block">Back</button>
  </form>
</div>

<script>
$(document).ready(function(){

  // Show teacher form
  $('#teacher-btn').click(function(){
    $('#login-options').hide();
    $('#teacher-frm').fadeIn();
  });

  // Show student form
  $('#student-btn').click(function(){
    $('#login-options').hide();
    $('#student-frm').fadeIn();
  });

  // Back buttons
  $('#back-btn1, #back-btn2').click(function(){
    $('#teacher-frm, #student-frm').hide();
    $('#login-options').fadeIn();
  });

  // Toggle password visibility
  $(".toggle-password").click(function() {
    let input = $($(this).attr("toggle"));
    if (input.attr("type") === "password") {
      input.attr("type", "text");
      $(this).removeClass("fa-eye").addClass("fa-eye-slash");
    } else {
      input.attr("type", "password");
      $(this).removeClass("fa-eye-slash").addClass("fa-eye");
    }
  });

  // Teacher login AJAX
  $('#teacher-frm').submit(function(e){
    e.preventDefault();
    $("#alert-msg-teacher").html("");
    $.ajax({
        url: 'ajax.php?action=teacherLogin',
        method: 'POST',
        data: $(this).serialize(),
        success: function(resp){
            if(resp == 1){
                window.location.href = 'teacher_dashboard.php';
            } else {
                $("#alert-msg-teacher").html('<div class="alert alert-danger">Invalid Email or Password.</div>');
            }
        },
        error: function(){
            $("#alert-msg-teacher").html('<div class="alert alert-warning">Server error. Try again later.</div>');
        }
    });
  });

  // Student login AJAX
  $('#student-frm').submit(function(e){
    e.preventDefault();
    $("#alert-msg-student").html("");
    $.ajax({
      url:'ajax.php?action=login2',
      method:'POST',
      data:$(this).serialize(),
      success: function(resp){
        if(resp == 1){
          location.href ='student_results.php';
        } else if(resp == 2){
          $("#alert-msg-student").html('<div class="alert alert-warning">هیڤدارین ب زیترین ده‌م سه‌ردانا قوتابخانا مه‌ بكه‌ن بوو دانان قستا</div>');
        } else {
          $("#alert-msg-student").html('<div class="alert alert-danger">Invalid Student Code.</div>');
        }
      }
    });
  });

});
</script>

</body>
</html>
