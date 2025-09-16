<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./db_connect.php');
ob_start();
$system = $conn->query("SELECT * FROM system_settings")->fetch_array();
foreach($system as $k => $v){
  $_SESSION['system'][$k] = $v;
}
ob_end_flush();

if(isset($_SESSION['login_id']))
  header("location:index.php?page=home");
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Login | <?php echo $_SESSION['system']['name'] ?></title>
  <?php include('./header.php'); ?>
  <style>
    body{
      width: 100%;
      height: 100%;
      background-color: #121212;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-card {
      background-color: #1f1f1f;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.6);
      width: 100%;
      max-width: 400px;
      color: #fff;
      text-align: center;
      position: relative;
    }
    .login-card img.icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin-bottom: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.5);
    }
    .login-card h4 {
      margin-bottom: 25px;
      font-weight: bold;
    }
    .login-card .form-control {
      border-radius: 8px;
      padding: 10px 12px;
      background-color: #2c2c2c;
      border: 1px solid #444;
      color: #fff;
      margin-bottom: 15px;
    }
    .login-card .form-control:focus {
      border-color: #28a745;
      box-shadow: none;
      background-color: #2c2c2c;
      color: #fff;
    }
    .login-card .btn {
      border-radius: 8px;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .login-card .btn-primary:hover {
      background-color: #218838;
      border-color: #218838;
    }
    .login-card .alert {
      margin-bottom: 15px;
    }
  </style>
</head>

<body>
  <div class="login-card">
    <img src="assets/brayan-icon.jpg" class="icon" alt="Logo">
    <h4><?php echo $_SESSION['system']['name'] ?> - Admin Login</h4>
    <form id="login-form">
      <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
      <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>

<?php include 'footer.php' ?>
<script>
  // Admin login
  $('#login-form').submit(function(e){
      e.preventDefault();
      $('#login-form .alert').remove();
      $.ajax({
          url:'ajax.php?action=login',
          method:'POST',
          data:$(this).serialize(),
          success:function(resp){
              if(resp == 1){
                  location.href ='index.php?page=home';
              } else {
                  $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>')
              }
          }
      });
  });
</script>
</body>
</html>
