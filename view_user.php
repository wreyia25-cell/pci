<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
    $type_arr = array('',"Admin","User");
    $qry = $conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM users where id = ".$_GET['id'])->fetch_array();
    foreach($qry as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid d-flex justify-content-center my-4">
    <div class="card shadow-lg" style="max-width: 400px; width: 100%; border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-dark text-center position-relative" style="height: 120px;">
            <img src="assets/brayan-icon.jpg" alt="Icon" style="position: absolute; top: -40px; left: calc(50% - 40px); width: 80px; height: 80px; border-radius: 50%; border: 3px solid #fff;">
            <h3 class="text-white mt-5 pt-3"><?php echo ucwords($name) ?></h3>
            <h6 class="text-light"><?php echo $email ?></h6>
        </div>
        <div class="card-body text-center">
            <div class="mb-3">
                <?php if(empty($avatar) || (!empty($avatar) && !is_file('../assets/uploads/'.$avatar))): ?>
                    <span class="d-inline-flex justify-content-center align-items-center bg-primary text-white font-weight-bold rounded-circle" 
                        style="width: 90px; height: 90px; font-size: 28px; margin-top: -60px;">
                        <?php echo strtoupper(substr($firstname, 0,1).substr($lastname, 0,1)) ?>
                    </span>
                <?php else: ?>
                    <img class="rounded-circle shadow" src="../assets/uploads/<?php echo $avatar ?>" alt="User Avatar" style="width: 90px; height: 90px; margin-top: -60px;">
                <?php endif ?>
            </div>
            <div class="text-left mt-3">
                <dl class="row">
                    <dt class="col-5 font-weight-bold">Address:</dt>
                    <dd class="col-7"><?php echo $address ?></dd>
                </dl>
                <dl class="row">
                    <dt class="col-5 font-weight-bold">User Type:</dt>
                    <dd class="col-7"><?php echo $type_arr[$type] ?></dd>
                </dl>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-center bg-light">
            <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<style>
    #uni_modal .modal-footer {
        display: none;
    }
    #uni_modal .modal-footer.display {
        display: flex;
    }
</style>
