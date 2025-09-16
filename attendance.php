<?php
// attendance.php
include('header.php');
?>

<div class="container" style="margin-top:30px">
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-9">Attendance List</div>
        <div class="col-md-3" align="right">
          <button type="button" id="report_button" class="btn btn-danger btn-sm">Report</button>
          <button type="button" id="add_button" class="btn btn-info btn-sm">Add</button>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <span id="message_operation"></span>
        <table class="table table-striped table-bordered" id="attendance_table">
          <thead>
            <tr>
              <th>Student Name</th>
              <th>Roll Number</th>
              <th>Grade</th>
              <th>Attendance Status</th>
              <th>Attendance Date</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal" id="formModal">
  <div class="modal-dialog">
    <form method="post" id="attendance_form">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="modal_title"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Grade <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <select name="grade_id" id="grade_id" class="form-control" required>
                  <option value="">Select Grade</option>
                  <?php
                  $query = "SELECT g.* FROM tbl_grade g INNER JOIN tbl_teacher_grade tg ON g.grade_id = tg.grade_id WHERE tg.teacher_id = '".$_SESSION["teacher_id"]."'";
                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $result = $statement->fetchAll();
                  foreach ($result as $row) {
                      echo '<option value="' . $row["grade_id"] . '">' . $row["grade_name"] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <label class="col-md-4 text-right">Attendance Date <span class="text-danger">*</span></label>
              <div class="col-md-8">
                <input type="text" name="attendance_date" id="attendance_date" class="form-control" readonly />
                <span id="error_attendance_date" class="text-danger"></span>
              </div>
            </div>
          </div>

          <div class="form-group" id="student_details"></div>
        </div>

        <div class="modal-footer">
          <input type="hidden" name="action" id="action" value="Add" />
          <input type="submit" name="button_action" id="button_action" class="btn btn-success btn-sm" value="Add" />
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
        </div>

      </div>
    </form>
  </div>
</div>

<!-- Report Modal -->
<div class="modal" id="reportModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title">Make Report</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <div class="form-group">
          <div class="input-daterange">
            <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly />
            <span id="error_from_date" class="text-danger"></span>
            <br />
            <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
            <span id="error_to_date" class="text-danger"></span>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" name="create_report" id="create_report" class="btn btn-success btn-sm">Create Report</button>
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" href="css/datepicker.css" />

<style>
.datepicker { z-index:1600 !important; }
</style>

<script>
$(document).ready(function(){

  // Initialize DataTable
  var dataTable = $('#attendance_table').DataTable({
    "processing":true,
    "serverSide":true,
    "order":[],
    "ajax":{
      url:"attendance_action.php",
      method:"POST",
      data:{action:"fetch"}
    }
  });

  // Initialize datepicker for report modal
  $('.input-daterange').datepicker({
    todayBtn:"linked",
    format:"yyyy-mm-dd",
    autoclose:true,
    container: '#formModal modal-body'
  });

  // Auto-fill current date and time for Add modal
  function getCurrentDateTime(){
    var now = new Date();
    var year = now.getFullYear();
    var month = ("0" + (now.getMonth()+1)).slice(-2);
    var day = ("0" + now.getDate()).slice(-2);
    var hours = ("0" + now.getHours()).slice(-2);
    var minutes = ("0" + now.getMinutes()).slice(-2);
    var seconds = ("0" + now.getSeconds()).slice(-2);
    return year+"-"+month+"-"+day+" "+hours+":"+minutes+":"+seconds;
  }

  function clear_field()
  {
    $('#attendance_form')[0].reset();
    $('#error_attendance_date').text('');
  }

  // Show Add Attendance modal
  $('#add_button').click(function(){
    $('#modal_title').text("Add Attendance");
    $('#formModal').modal('show');
    clear_field();
    $('#attendance_date').val(getCurrentDateTime());
  });

  // Fetch students for selected grade
  $('#grade_id').change(function(){
    var grade_id = $(this).val();
    if(grade_id != ''){
      $.ajax({
        url:'fetch_students.php',
        method:'POST',
        data:{grade_id:grade_id},
        success:function(data){
          $('#student_details').html(data);
        }
      });
    } else {
      $('#student_details').html('');
    }
  });

  // Submit Add/Edit Attendance
  $('#attendance_form').on('submit', function(event){
    event.preventDefault();
    $.ajax({
      url:"attendance_action.php",
      method:"POST",
      data:$(this).serialize(),
      dataType:"json",
      beforeSend:function(){
        $('#button_action').val('Processing...');
        $('#button_action').attr('disabled','disabled');
      },
      success:function(data){
        $('#button_action').attr('disabled', false);
        $('#button_action').val($('#action').val());
        if(data.success){
          $('#message_operation').html('<div class="alert alert-success">'+data.success+'</div>');
          clear_field();
          $('#formModal').modal('hide');
          dataTable.ajax.reload();
        }
        if(data.error){
          if(data.error_attendance_date != ''){
            $('#error_attendance_date').text(data.error_attendance_date);
          } else {
            $('#error_attendance_date').text('');
          }
        }
      }
    });
  });

  // Show Report modal
  $('#report_button').click(function(){
    $('#reportModal').modal('show');
  });

  // Create report
  $('#create_report').click(function(){
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    var error = 0;
    if(from_date == ''){ $('#error_from_date').text('From Date is Required'); error++; } else { $('#error_from_date').text(''); }
    if(to_date == ''){ $('#error_to_date').text('To Date is Required'); error++; } else { $('#error_to_date').text(''); }
    if(error == 0){
      $('#from_date').val(''); $('#to_date').val('');
      $('#reportModal').modal('hide');
      window.open("report.php?action=attendance_report&from_date="+from_date+"&to_date="+to_date);
    }
  });

});
</script>
