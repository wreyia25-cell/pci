<?php
include 'db_connect.php';

$result_id = intval($_GET['id']);
$student_id = intval($_GET['student_id']);

// Get result note
$qry = $conn->query("SELECT note FROM results WHERE id = $result_id")->fetch_assoc();
$note = $qry['note'] ?? '';

// Get student's status
$student = $conn->query("SELECT isActive FROM students WHERE id = $student_id")->fetch_assoc();
$isActive = $student['isActive'] ?? 0;
?>

<div class="container-fluid">
    <form id="noteForm">
        <input type="hidden" name="id" value="<?php echo $result_id; ?>">
        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">

        <div class="form-group">
            <label for="note">Note:</label>
            <textarea name="note" id="note" class="form-control" rows="4"><?php echo htmlspecialchars($note); ?></textarea>
        </div>

        <div class="form-group">
            <label for="isActive">He paid the installment :</label>
            <select name="isActive" id="isActive" class="form-control">
                <option value="1" <?php echo ($isActive == 1)? 'selected' : ''; ?>>Yes</option>
                <option value="0" <?php echo ($isActive == 0)? 'selected' : ''; ?>>No</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script>
$('#noteForm').submit(function(e){
    e.preventDefault();
    $.ajax({
        url:'ajax.php?action=update_note',
        method:'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success:function(resp){
            if(resp.status == 1){
                alert_toast("Note & Status updated successfully", 'success');
                uni_modal_hide();
                setTimeout(function(){ location.reload(); }, 500);
            }else{
                let msg = "Failed to update";
                if(resp.errors){
                    msg += ": " + resp.errors.join(", ");
                }
                alert_toast(msg, 'error');
            }
        },
        error:function(xhr){
            alert_toast("AJAX error: " + xhr.statusText, 'error');
        }
    });
});
</script>
