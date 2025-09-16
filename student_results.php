<!DOCTYPE html>
<html lang="en">
<?php session_start() ?>
<?php 
 include 'db_connect.php';
 if(!isset($_SESSION['rs_id']))
      header('location:login.php');
    include 'db_connect.php';
    ob_start();
  if(!isset($_SESSION['system'])){
    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
    }
  }
  ob_end_flush();
  include 'header.php' 
?>
<style>
  body {
    background: linear-gradient(135deg, #e0f7fa, #ffffff);
    font-family: 'Segoe UI', sans-serif;
    color: #2c3e50;
  }
  .content-wrapper {
    background: transparent;
    padding: 20px;
  }
  .card-custom {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
    animation: fadeInUp 0.7s ease;
    color: #2c3e50;
  }
  .modal-content {
    border-radius: 15px;
    background: #ffffff;
    color: #2c3e50;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
  }
  .modal-header {
    background: #4fc3f7;
    color: #ffffff;
    border-radius: 15px 15px 0 0;
  }
  .btn-primary {
    background: #4fc3f7;
    border: none;
    color: #ffffff;
  }
  .btn-primary:hover {
    background: #29b6f6;
  }
  .btn-secondary {
    background: #b0bec5;
    border: none;
    color: #ffffff;
  }
  .toast {
    border-radius: 10px;
  }
  footer.main-footer {
    background: transparent;
    color: #2c3e50;
    text-align: center;
    padding: 15px;
    border: none;
    font-size: 14px;
  }
  @keyframes fadeInUp {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
  }

  /* Responsive */
  @media (max-width: 768px) {
    .card-custom {
      padding: 15px;
    }
    .modal-dialog {
      max-width: 95%;
      margin: auto;
    }
  }
</style>
<body class="hold-transition layout-fixed layout-navbar-fixed layout-footer-fixed sidebar-collapse">
<div class="wrapper">
  <?php include 'topbar.php' ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
     <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body text-white"></div>
    </div>
    <div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>
    
    <!-- Main content -->
    <section class="content">
      <div class="container-md py-2">
        <div class="card-custom">
         <?php include 'results.php'; ?>
        </div>
      </div>
    </section>
    <!-- /.content -->

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirm_modal" role='dialog'>
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmation</h5>
          </div>
          <div class="modal-body">
            <div id="delete_content"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Universal Modal -->
    <div class="modal fade" id="uni_modal" role='dialog'>
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"></h5>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Modal -->
    <div class="modal fade" id="uni_modal_right" role='dialog'>
      <div class="modal-dialog modal-full-height modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span class="fa fa-arrow-right"></span>
            </button>
          </div>
          <div class="modal-body"></div>
        </div>
      </div>
    </div>

    <!-- Viewer Modal -->
    <div class="modal fade" id="viewer_modal" role='dialog'>
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
          <img src="" alt="">
        </div>
      </div>
    </div>

  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer" style="position: static !important;">
    <div><b><?php echo $_SESSION['system']['name'] ?></b></div>
  </footer>
</div>
<!-- ./wrapper -->

<?php include 'footer.php' ?>
</body>
</html>
