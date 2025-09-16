<?php include'db_connect.php'; ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title">Add New Teacher</h5>
		</div>
		<div class="card-body">
			<form id="manage-teacher">
				
				<div class="row">
					
					<div class="col-md-6">
						<div class="form-group">
							<label>First Name</label>
							<input type="text" name="first_name" class="form-control" required>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Last Name</label>
							<input type="text" name="last_name" class="form-control" required>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Email</label>
							<input type="email" name="email" class="form-control" required>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Phone Number</label>
							<input type="text" name="phone_number" class="form-control">
						</div>
					</div>
					<div class="col-md-10">
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
</div>

				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label>Hire Date</label>
							<input type="date" name="hire_date" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Qualification</label>
							<input type="text" name="qualification" class="form-control">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label>Subject Specialization</label>
					<select name="subject_id[]" class="form-control select2" multiple="multiple" required>
						<?php
						$classes = $conn->query("SELECT * FROM subjects");
						while($row = $classes->fetch_assoc()):
						?>
							<option value="<?php echo $row['id'] ?>"><?php echo $row['subject']?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="form-group">
					<label>Assign Classes</label>
					<select name="class_id[]" class="form-control select2" multiple="multiple" required>
						<?php
						$classes = $conn->query("SELECT * FROM classes ORDER BY level, section");
						while($row = $classes->fetch_assoc()):
						?>
							<option value="<?php echo $row['id'] ?>"><?php echo $row['level'].' - '.$row['section'] ?></option>
						<?php endwhile; ?>
					</select>
					<small class="text-muted">Hold CTRL to select multiple</small>
				</div>
				<div class="form-group text-center">
					<button class="btn btn-primary btn-flat">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
$('#manage-teacher').submit(function(e){
    e.preventDefault();
    start_load();
    $.ajax({
        url:'ajax.php?action=save_teacher',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        success:function(resp){
            end_load();
            if(resp == 1){
                alert_toast("Teacher successfully added",'success');
                setTimeout(function(){
                    location.href = 'index.php?page=teacher_list';
                },1500);
            }else{
                // Show SQL error if exists
                alert_toast("Error saving data: "+resp,'error');
            }
        },
        error: function(xhr, status, error){
            end_load();
            alert_toast("AJAX Error: "+error,'error');
        }
    });
});

</script>
