<?php include 'db_connect.php'; ?>
<div class="col-lg-12 position-relative" style="min-height:500px;">

    <!-- Watermark -->
    <div style="
        position:absolute;
        top:50%;
        left:50%;
        transform:translate(-50%, -50%);
        opacity:0.05;
        z-index:0;
        pointer-events:none;
    ">
        <img src="assets/brayan-icon.jpg" alt="Watermark" style="max-width:300px; max-height:300px;">
    </div>

    <div class="card card-outline card-primary" style="position:relative; z-index:1;">
        <div class="card-body">
            <table class="table table-hover table-bordered" id="list">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Student Name</th>
                        <th>Student Code</th>
                        <th>Class</th>
                        <th>Total Subjects</th>
                        <th>Note</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
  				<?php
$i = 1;
$where = "";
if (isset($_SESSION['rs_id'])) {
    $where = " AND r.student_id = {$_SESSION['rs_id']} ";
}

$qry = $conn->query("
    SELECT s.id AS student_id,
           CONCAT(s.firstname,' ',s.middlename,' ',s.lastname) AS name,
           s.student_code,
           CONCAT(c.level,'-',c.section) AS class,
           s.isActive,
           r.id AS latest_result_id,
           r.note
    FROM students s
    INNER JOIN classes c ON c.id = s.class_id
    INNER JOIN results r 
        ON r.id = (
            SELECT id FROM results 
            WHERE student_id = s.id
            ORDER BY date_created DESC LIMIT 1
        )
    $where
    ORDER BY name ASC
");

while($row = $qry->fetch_assoc()):
    $subjects = $conn->query("
        SELECT COUNT(*) AS total
        FROM result_items ri
        WHERE ri.result_id = ".$row['latest_result_id']."
    ")->fetch_assoc()['total'];
?>
<tr>
    <th class="text-center"><?php echo $i++; ?></th>
    <td><?php echo ucwords($row['name']); ?></td>
    <td><?php echo $row['student_code']; ?></td>
    <td><?php echo ucwords($row['class']); ?></td>
    <td class="text-center"><?php echo $subjects; ?></td>
    <td class="text-center"><?php echo $row['note'] ?: '-'; ?></td>
    <td class="text-center">
        <?php if($row['isActive']): ?>
            <span class="badge badge-success">Active</span>
        <?php else: ?>
            <span class="badge badge-danger">Inactive</span>
        <?php endif; ?>
    </td>
    <td class="text-center">
        <button data-id="<?php echo $row['latest_result_id']; ?>" class="btn btn-info btn-flat view_result">
            <i class="fas fa-eye"></i> View Result
        </button>
        <?php if (!isset($_SESSION['rs_id'])) { ?>
        <button data-id="<?php echo $row['latest_result_id']; ?>" 
                data-student="<?php echo $row['student_id']; ?>" 
                class="btn btn-warning btn-flat edit_note">
            <i class="fas fa-edit"></i> Edit Note
        </button>
        <?php } ?>
    </td>
</tr>
<?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#list').DataTable();

    // View Result modal
    $('.view_result').click(function(){
        var result_id = $(this).data('id');
        uni_modal("Student Result", "view_result.php?id=" + result_id, 'mid-large');
    });

    // Edit Note modal
    $('.edit_note').click(function(){
        var result_id = $(this).data('id');
        var student_id = $(this).data('student');
        uni_modal("Edit Note", "edit_note.php?id=" + result_id + "&student_id=" + student_id, 'mid-small');
    });
});
</script>
