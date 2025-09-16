<?php
session_start();
if(!isset($_SESSION['teacher_id'])){
    header("Location: teacher_login.php");
    exit();
}

include 'db_connect.php';
$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher's classes
$classes_query = $conn->query("
    SELECT c.id, CONCAT(c.level, '-', c.section) AS class_name
    FROM classes c
    INNER JOIN teacher_class tc ON c.id = tc.class_id
    WHERE tc.teacher_id = '$teacher_id'
");

// Fetch teacher's subjects
$subjects_query = $conn->query("
    SELECT s.id, s.subject
    FROM subjects s
    INNER JOIN teacher_subject ts ON s.id = ts.subject_id
    WHERE ts.teacher_id = '$teacher_id'
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #e0f7fa, #ffffff); 
            font-family: 'Segoe UI', sans-serif; 
            color: #2c3e50; 
        }
        .container { max-width: 1200px; }

        .dashboard-header {
            background: linear-gradient(135deg, #4fc3f7, #29b6f6);
            color: white; 
            padding: 20px; 
            border-radius: 12px; 
            margin-bottom: 25px;
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .dashboard-header h1 { font-size: 1.8rem; font-weight: bold; }
        .logout-btn {
            background: white; 
            color: #4fc3f7; 
            font-weight: bold; 
            border-radius: 8px;
            padding: 8px 15px;
            transition: all 0.3s;
        }
        .logout-btn:hover { background: #e0f7fa; color: #2196f3; }

        .card { 
            border-radius: 12px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.05); 
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .card:hover { transform: translateY(-3px); }

        .subject-radio { margin-right: 15px; }
        input[type="radio"] { transform: scale(1.3); cursor: pointer; }

        #studentsTable { width: 100%; }
        #studentsTable tbody tr:hover { background-color: #f1faff; }
        #studentsTable thead th { background-color: #b3e5fc; color: #0277bd; }
        #studentsTable thead th:nth-child(6) { background-color: #d0f8ce; color: #1b5e20; }
        #studentsTable thead th:nth-child(7) { background-color: #ffcdd2; color: #b71c1c; }

        #saveRatings, #printTable { border-radius: 8px; padding: 10px 20px; }

        .student-avatar {
            font-weight: bold; font-size: 1rem;
            width: 40px; height: 40px; line-height: 40px;
            border-radius: 50%; text-align: center;
            color: #fff; background-color: #03a9f4; margin-right: 10px;
        }

        @media (max-width: 768px) {
            .dashboard-header { flex-direction: column; text-align: center; }
            .dashboard-header h1 { margin-top: 10px; }
            .card { padding: 15px; }
        }
    </style>
</head>
<body class="p-4">
<div class="container">
    <div class="dashboard-header">
        <div class="d-flex align-items-center justify-content-center">
            <img src="assets/brayan-icon.jpg" alt="Dashboard Icon" style="width:50px; height:50px; border-radius:50%; margin-right:15px; object-fit:cover;">
            <h1>Brayan Private School</h1>
        </div>
        <a href="teacher_logout.php" class="btn logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="mb-4">
        <h4>Welcome, <?php echo htmlspecialchars($_SESSION['teacher_first_name'].' '.$_SESSION['teacher_last_name']); ?></h4>
        <p class="text-muted mb-0">
            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($_SESSION['teacher_email']); ?>
            <?php if(isset($_SESSION['teacher_phone_number'])): ?>
                <br><i class="fas fa-phone"></i> <?php echo htmlspecialchars($_SESSION['teacher_phone_number']); ?>
            <?php endif; ?>
        </p>
    </div>

    <div class="card p-3 mb-4">
        <h5>Your Subjects</h5>
        <?php if($subjects_query->num_rows > 0): ?>
            <?php while($row = $subjects_query->fetch_assoc()): ?>
                <div class="form-check form-check-inline subject-radio">
                    <input class="form-check-input" type="radio" name="subject" value="<?php echo $row['id']; ?>">
                    <label class="form-check-label"><?php echo htmlspecialchars($row['subject']); ?></label>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-muted">No subjects assigned yet.</p>
        <?php endif; ?>
    </div>

    <div class="card p-3 mb-4">
        <h5>Select Class</h5>
        <select id="classSelect" class="form-select w-50">
            <option value="">-- Select Class --</option>
            <?php while($row = $classes_query->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['class_name']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="card p-3">
        <h5>Students</h5>
        <table class="table table-bordered" id="studentsTable">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Excellent</th>
                    <th>Very Good</th>
                    <th>Good</th>
                    <th>Not Good</th>
                    <th>Present</th>
                    <th>Absent</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="7" class="text-center text-muted">Please select a class to view students.</td></tr>
            </tbody>
        </table>
        <div class="mt-3">
            <button id="saveRatings" class="btn btn-success">Save Ratings</button>
            <button id="printTable" class="btn btn-primary">Print Table</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    var selectedSubject = null;

    $('input[name="subject"]').change(function(){
        selectedSubject = $(this).val();
    });

    $('#classSelect').change(function(){
        var classId = $(this).val();
        if(classId){
            $.ajax({
                url: 'fetch_students.php',
                type: 'POST',
                data: { class_id: classId },
                success: function(data){
                    $('#studentsTable tbody').html(data);
                }
            });
        } else {
            $('#studentsTable tbody').html('<tr><td colspan="7" class="text-center text-muted">Please select a class to view students.</td></tr>');
        }
    });

    $('#saveRatings').click(function(){
        var classId = $('#classSelect').val();
        if(!classId){ alert('Please select a class.'); return; }
        if(!selectedSubject){ alert('Please select a subject.'); return; }

        var ratings = {};
        var attendance = {};

        $('#studentsTable tbody tr').each(function(){
            var studentId = $(this).find('input[type=radio][name^="rating"]').attr('name')?.match(/\d+/)[0];
            if(!studentId) return;

            var ratingVal = $(this).find('input[name="rating['+studentId+']"]:checked').val();
            if(ratingVal) ratings[studentId] = ratingVal;

            var attendanceVal = $(this).find('input[name="attendance['+studentId+']"]:checked').val() || 'Absent';
            attendance[studentId] = attendanceVal;
        });

        if(Object.keys(ratings).length === 0){ alert('Please select ratings for at least one student.'); return; }

        $.post('save_ratings.php', { rating: ratings, attendance: attendance, class_id: classId, subject_id: selectedSubject }, function(response){
            alert(response);
        });
    });

    // Print functionality
    $('#printTable').click(function(){
        var printTable = $('#studentsTable').clone();

        printTable.find('tbody tr').each(function(){
            $(this).find('td').each(function(index){
                if(index >=1 && index <=4){ // ratings
                    var checked = $(this).find('input[type=radio]:checked');
                    if(checked.length){
                        var val = checked.val();
                        $(this).html(val + (val==='Excellent' ? ' ⭐' : val==='Very Good' ? ' 👍' : val==='Good' ? ' 🙂' : ' ⚠️'));
                    }else{
                        $(this).html('');
                    }
                }
                if(index >=5 && index <=6){ // attendance
                    var checked = $(this).find('input[type=radio]:checked');
                    if(checked.length){
                        $(this).html(checked.val());
                    }else{
                        $(this).html('');
                    }
                }
            });
        });

        var newWin = window.open('', '', 'width=1000,height=600');
        newWin.document.write('<html><head><title>Print Students Table</title>');
        newWin.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
        newWin.document.write('</head><body>');
        newWin.document.write(printTable.prop('outerHTML'));
        newWin.document.write('</body></html>');
        newWin.document.close();
        newWin.print();
    });

});
</script>
</body>
</html>
