<?php include'db_connect.php' ?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_teacher">
                    <i class="fa fa-plus"></i> Add New
                </a>
			</div>
		</div>
		<div class="card-body">
			<table class="table table-hover table-bordered" id="teacher_list">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Classes</th>
						<th>Subjects</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("
                        SELECT t.id AS id, CONCAT(t.first_name,' ',t.last_name) as name, t.email,
                               GROUP_CONCAT(DISTINCT CONCAT(c.level,'-',c.section) SEPARATOR ', ') as classes,
                               GROUP_CONCAT(DISTINCT s.subject SEPARATOR ', ') as subjects
                        FROM teachers t
                        LEFT JOIN teacher_class tc ON tc.teacher_id = t.id
                        LEFT JOIN classes c ON tc.class_id = c.id
                        LEFT JOIN teacher_subject ts ON ts.teacher_id = t.id
                        LEFT JOIN subjects s ON ts.subject_id = s.id
                        GROUP BY t.id
                        ORDER BY t.first_name, t.last_name ASC
                    ");
					while($row = $qry->fetch_assoc()):
					?>
					<tr>
						<td class="text-center"><?php echo $i++ ?></td>
						<td><b><?php echo $row['id'] ?></b></td>
						<td><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['email'] ?></b></td>
						<td><b><?php echo $row['classes'] ? $row['classes'] : 'N/A' ?></b></td>
						<td><b><?php echo $row['subjects'] ? $row['subjects'] : 'N/A' ?></b></td>
						<td class="text-center">
		                    <div class="btn-group">
		                        <a href="index.php?page=edit_teacher&id=<?php echo $row['id'] ?>" class="btn btn-primary btn-flat">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                        <button type="button" class="btn btn-danger btn-flat delete_teacher" data-id="<?php echo $row['id'] ?>">
		                          <i class="fas fa-trash"></i>
		                        </button>
	                      	</div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<style>
	table td{
		vertical-align: middle !important;
	}
</style>

<script>
	$(document).ready(function(){
		$('#teacher_list').dataTable();

		$('.delete_teacher').click(function(){
			_conf("Are you sure to delete this Teacher?","delete_teacher",[$(this).attr('data-id')])
		})
	})

	function delete_teacher($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_teacher',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
</script>
