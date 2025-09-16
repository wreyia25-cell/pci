<?php if(!isset($conn)){ include 'db_connect.php'; } ?>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="manage-result">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">

        <div class="row justify-content-center">
          <div class="col-md-4">
            <div id="msg" class=""></div>
            <div class="form-group">
              <label for="level">Level</label>
              <select name="level" id="level" class="form-control select2 select2-sm">
                <option value="">Select Level</option>
                <?php 
                  $levels = $conn->query("SELECT DISTINCT level FROM classes ORDER BY level ASC");
                  while($row = $levels->fetch_assoc()):
                ?>
                  <option value="<?php echo $row['level'] ?>" <?php echo isset($level) && $level==$row['level']?'selected':'' ?>><?php echo $row['level'] ?></option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="section">Section</label>
              <select name="section" id="section" class="form-control select2 select2-sm">
                <option value="">Select Section</option>
                <?php 
                  $sections = $conn->query("SELECT DISTINCT section FROM classes ORDER BY section ASC");
                  while($row = $sections->fetch_assoc()):
                ?>
                  <option value="<?php echo $row['section'] ?>" <?php echo isset($section) && $section==$row['section']?'selected':'' ?>><?php echo $row['section'] ?></option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="student_id">Student</label>
              <select name="student_id" id="student_id" class="form-control select2 select2-sm" required>
                <option value="">Select Student</option>
                <?php 
                  if(isset($id) && isset($student_id)){
                    $s = $conn->query("SELECT s.id, s.student_code, CONCAT(s.firstname,' ',s.middlename,' ',s.lastname) AS name, c.id AS class_id, CONCAT(c.level,'-',c.section) AS class 
                                        FROM students s 
                                        INNER JOIN classes c ON s.class_id=c.id 
                                        WHERE s.id='$student_id'")->fetch_assoc();
                    echo "<option value='".$s['id']."' data-class_id='".$s['class_id']."' data-class='".$s['class']."' selected>".$s['student_code']." | ".ucwords($s['name'])."</option>";
                  }
                ?>
              </select>
              <small id="class"><?php echo isset($class) ? "Current Class: ".$class : "" ?></small>
              <input type="hidden" name="class_id" value="<?php echo isset($class_id) ? $class_id: '' ?>">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="week_no">Week Number</label>
              <input type="number" name="week_no" id="week_no" class="form-control" value="<?php echo isset($week_no)?$week_no:'' ?>" min="1" max="52" required>
            </div>
          </div>

          <div class="col-md-8">
            <div class="form-group">
              <label for="note">Note</label>
              <textarea name="note" id="note" class="form-control" rows="2"><?php echo isset($note)?$note:'' ?></textarea>
            </div>
          </div>
        </div>

        <hr>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="subject_id">Subject</label>
              <select name="" id="subject_id" class="form-control select2 select2-sm">
                <option value="">Select Subject</option>
                <?php 
                  $subjects = $conn->query("SELECT * FROM subjects ORDER BY subject ASC");
                  while($row = $subjects->fetch_assoc()):
                ?>
                  <option value="<?php echo $row['id'] ?>" data-json='<?php echo json_encode($row) ?>'><?php echo $row['subject_code'].' | '.ucwords($row['subject']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label for="status-id">Status</label>
              <select name="status" id="status-id" class="form-control select2 select2-sm">
                <option value="">Select</option>
                <option value="Excellent">Excellent</option>
                <option value="Very Good">Very Good</option>
                <option value="Good">Good</option>
                <option value="Not Good">Not Good</option>
              </select>
            </div>
          </div>

          <div class="col-md-2">
            <button class="btn btn-sm btn-primary mt-4" type="button" id="add-status">Add</button>
          </div>
        </div>

        <div class="col-md-12">
          <table class="table table-bordered" id="mark-list">
            <thead>
              <tr>
                <th>Subject Code</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if(isset($id)):
                $items=$conn->query("SELECT r.*,s.subject_code,s.subject,s.id as sid FROM result_items r INNER JOIN subjects s ON s.id=r.subject_id WHERE result_id=$id ORDER BY s.subject_code ASC");
                while($row = $items->fetch_assoc()):
              ?>
                <tr data-id="<?php echo $row['sid'] ?>">
                  <td><input type="hidden" name="subject_id[]" value="<?php echo $row['subject_id'] ?>"><?php echo $row['subject_code'] ?></td>
                  <td><?php echo ucwords($row['subject']) ?></td>
                  <td><input type="hidden" name="status[]" value="<?php echo $row['stautus'] ?>"><?php echo $row['stautus'] ?></td>
                  <td class="text-center"><button class="btn btn-sm btn-danger" type="button" onclick="$(this).closest('tr').remove()"><i class="fa fa-times"></i></button></td>
                </tr>
              <?php endwhile; endif; ?>
            </tbody>
          </table>
        </div>

      </form>
		</div>

		<div class="card-footer border-top border-info">
			<div class="d-flex w-100 justify-content-center align-items-center">
				<button class="btn btn-flat bg-gradient-primary mx-2" form="manage-result">Save</button>
				<a class="btn btn-flat bg-gradient-secondary mx-2" href="./index.php?page=results">Cancel</a>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
  // fetch students when level or section changes
  $('#level,#section').change(function(){
    var level = $('#level').val();
    var section = $('#section').val();
    if(level && section){
      $.ajax({
        url:'ajax.php?action=get_students_by_class',
        method:'POST',
        data:{level:level,section:section},
        success:function(resp){
          $('#student_id').html(resp).trigger('change');
        }
      });
    }
  });

  // add subject & status
  $('#add-status').click(function(){
    var subject_id = $('#subject_id').val();
    var status = $('#status-id').val();
    if(subject_id=='' || status==''){
      alert_toast("Please select subject & status before adding","error");
      return false;
    }
    var sData = $('#subject_id option[value="'+subject_id+'"]').attr('data-json');
    sData = JSON.parse(sData);

    if($('#mark-list tr[data-id="'+subject_id+'"]').length > 0){
      alert_toast("Subject already on list","error");
      return false;
    }

    var tr = $('<tr data-id="'+subject_id+'"></tr>');
    tr.append('<td><input type="hidden" name="subject_id[]" value="'+subject_id+'">'+sData.subject_code+'</td>');
    tr.append('<td>'+sData.subject+'</td>');
    tr.append('<td><input type="hidden" name="status[]" value="'+status+'">'+status+'</td>');
    tr.append('<td class="text-center"><button class="btn btn-sm btn-danger" type="button" onclick="$(this).closest(\'tr\').remove()"><i class="fa fa-times"></i></button></td>');

    $('#mark-list tbody').append(tr);
    $('#subject_id').val('').trigger('change');
    $('#status-id').val('');
  });

  // submit form
  $('#manage-result').submit(function(e){
    e.preventDefault();
    start_load();
    $('button[form="manage-result"]').attr('disabled',true);
    $.ajax({
      url:'ajax.php?action=save_result',
      data:new FormData($(this)[0]),
      cache:false,
      contentType:false,
      processData:false,
      method:'POST',
      success:function(resp){
        end_load();
        $('button[form="manage-result"]').attr('disabled',false);
        if(resp==1){
          alert_toast('Data successfully saved',"success");
          setTimeout(function(){ location.href='index.php?page=results' },2000);
        } else if(resp==2){
          $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Student result for this week already exists.</div>');
        } else {
          alert_toast('An error occurred',"error");
        }
      }
    });
  });
});

</script>
