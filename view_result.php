<?php
include 'db_connect.php';

if(!isset($_GET['id'])){
    echo "Invalid result ID";
    exit;
}

$result_id = intval($_GET['id']);

// Fetch main result
$qry = $conn->query("
    SELECT r.*, CONCAT(s.firstname,' ',s.middlename,' ',s.lastname) AS name, 
           s.student_code, CONCAT(c.level,'-',c.section) AS class, s.gender,
           s.id AS student_id
    FROM results r
    INNER JOIN classes c ON c.id = r.class_id
    INNER JOIN students s ON s.id = r.student_id
    WHERE r.id = $result_id
")->fetch_assoc();

if(!$qry){
    echo "Result not found";
    exit;
}

$student_code = $qry['student_code'];
$class = $qry['class'];
$name = $qry['name'];
$gender = $qry['gender'];
$note = $qry['note'];
$student_id = $qry['student_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Result Report</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- html2canvas (needed for pdf export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7f9fc;
            color: #333;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #0077b6, #00b4d8);
            color: white;
            padding: 30px 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 3px solid white;
        }
        .header h1 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        /* Note box */
        .note-box {
            background: #fff3cd;
            border-left: 6px solid #ffc107;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 0.95rem;
            margin-top: 15px;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9rem;
            color: #666;
        }
        .footer hr {
            margin: 20px 0;
            border-color: #ddd;
        }

        /* Table */
        .table thead {
            background: #0077b6;
            color: #fff;
        }
        .table th, .table td {
            vertical-align: middle !important;
        }

        /* Buttons */
        .btn-custom {
            border-radius: 25px;
            padding: 8px 20px;
        }

        /* Watermark */
        .watermark-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 260px;
            height: 260px;
            background: url('assets/brayan-icon.jpg') no-repeat center center;
            background-size: contain;
            opacity: 0.05;
            z-index: 0;
        }
        #printable .card, #printable .header, #printable table {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>

<div class="container py-4 position-relative" id="printable">
    <div class="watermark-icon"></div>

    <!-- Header -->
    <div class="header shadow">
        <img src="assets/brayan-icon.jpg" alt="School Logo">
        <h1>Brayin Private School</h1>
        <small>Student Result Report</small>
    </div>

    <!-- Student Info -->
    <div class="card shadow mb-4">
        <div class="card-body bg-white">
            <div class="row mb-2">
                <div class="col-md-6"><strong>Student ID #:</strong> <?php echo $student_code ?></div>
                <div class="col-md-6"><strong>Class:</strong> <?php echo $class ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"><strong>Student Name:</strong> <?php echo ucwords($name) ?></div>
                <div class="col-md-6"><strong>Gender:</strong> <?php echo ucwords($gender) ?></div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0 text-center">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Attendance</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $items = $conn->query("
                    SELECT 
                        s.subject_code, 
                        s.subject, 
                        ri.stautus, 
                        ri.date_created,
                        ri.attendance_status
                    FROM subjects s
                    LEFT JOIN result_items ri 
                        ON ri.id = (
                            SELECT MAX(r2.id)
                            FROM result_items r2
                            INNER JOIN results r3 ON r2.result_id = r3.id
                            WHERE r2.subject_id = s.id
                              AND r3.student_id = $student_id
                        )
                    ORDER BY s.subject_code ASC
                ");

                while($row = $items->fetch_assoc()):
                ?>
                <tr>
                    <td class="fw-bold"><?php echo $row['subject_code'] ?></td>
                    <td><?php echo ucwords($row['subject']) ?></td>
                    <td>
                        <?php echo $row['stautus'] ? '<span class="badge bg-info">'.ucwords($row['stautus']).'</span>' : '<span class="badge bg-secondary">Not Rated</span>'; ?>
                    </td>
                    <td>
                        <?php echo isset($row['date_created']) ? date('d-m-Y', strtotime($row['date_created'])) : '-'; ?>
                    </td>
                    <td>
                        <?php
                        if(isset($row['attendance_status'])){
                            $status = strtolower($row['attendance_status']);
                            switch($status){
                                case 'present':
                                    echo '<span class="badge bg-success">Present</span>';
                                    break;
                                case 'absent':
                                    echo '<span class="badge bg-danger">Absent</span>';
                                    break;
                                case 'late':
                                    echo '<span class="badge bg-warning text-dark">Late</span>';
                                    break;
                                default:
                                    echo '<span class="badge bg-secondary">'.ucwords($status).'</span>';
                            }
                        } else {
                            echo '<span class="badge bg-secondary">Not Marked</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Note -->
    <div class="note-box shadow-sm">
        <strong>Note:</strong> <?php echo $note ?: 'No additional notes.'; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <hr>
        <p><b>Brayin Private School</b> &copy; <?php echo date('Y'); ?> | All Rights Reserved</p>
        <p>_________________________<br><em>Principal’s Signature</em></p>
    </div>
</div>

<!-- Action Buttons -->
<div class="text-center p-3">
    <button type="button" class="btn btn-success btn-custom me-2" id="print"><i class="fa fa-print"></i> Print</button>
    <button type="button" class="btn btn-primary btn-custom me-2" id="pdf"><i class="fa fa-file-pdf"></i> Export to PDF</button>
    <button type="button" class="btn btn-warning btn-custom" id="download"><i class="fa fa-download"></i> Download</button>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$('#print').click(function(){
    var printContent = document.getElementById('printable').innerHTML;
    var nw = window.open('', '', 'height=700,width=900');
    nw.document.write('<html><head><title>Print</title></head><body>' + printContent + '</body></html>');
    nw.document.close();
    nw.print();
});

// Export to PDF
$('#pdf').click(function(){
    const { jsPDF } = window.jspdf;
    html2canvas(document.querySelector("#printable")).then(canvas => {
        const imgData = canvas.toDataURL("image/png");
        const pdf = new jsPDF("p", "mm", "a4");
        const imgProps= pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
        pdf.save("student_result.pdf");
    });
});

// Download HTML
$('#download').click(function(){
    var content = document.getElementById('printable').outerHTML;
    var blob = new Blob([content], {type: "text/html"});
    var url = URL.createObjectURL(blob);
    var a = document.createElement("a");
    a.href = url;
    a.download = "student_result.html";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
});
</script>

</body>
</html>
