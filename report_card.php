<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit();
}

// Include database connection
require 'db.php';

// Fetch logged-in user's role and ID
$user_role = $_SESSION['role'];
$user_id = $_SESSION['user_id']; // This should be the user ID from your users table

// Initialize the students array
$students = [];

// --- Fetch students based on user role ---
if ($user_role === 'admin' || $user_role === 'teacher') {
    // For admin and teacher, fetch all students
    // SECURITY NOTE: Admins and teachers are assumed to have permission to view all students.
    $sql = "SELECT id, name FROM students ORDER BY name ASC";
    $stmt = $conn->prepare($sql); // Use prepared statement even for no parameters for consistency

    if ($stmt === false) {
        die("SQL statement preparation failed (Admin/Teacher): " . $conn->error);
    }

    $stmt->execute();
    $students_result = $stmt->get_result();

    if ($students_result && $students_result->num_rows > 0) {
        while ($row = $students_result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    $stmt->close();

} elseif ($user_role === 'parent') {
    // For parent, fetch only children linked to this parent
    $sql = "
        SELECT s.id, s.name
        FROM students s
        JOIN parent_child_relationship pcr ON s.id = pcr.child_id
        WHERE pcr.parent_id = ?
        ORDER BY s.name ASC";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("SQL statement preparation failed (Parent): " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $students_result = $stmt->get_result();

    if ($students_result && $students_result->num_rows > 0) {
        while ($row = $students_result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    $stmt->close();

} else {
    // Handle other roles or users without a defined role appropriately
    die("Access denied: Your role is not authorized to view this page.");
}

// Initialize report card and attendance data arrays
$report_card = [];
$attendance_data = [];
$average_grade = 0; // Initialize average grade
$total_grades = 0; // Initialize total grades for average calculation
$grade_count = 0; // Initialize count of grades for average calculation

// Flag to track if the selected student is authorized for the current user
$is_authorized_student = false;
$selected_student_name = ""; // To display the student's name in the report

// If a student ID is selected via the form, fetch data for that student
if (isset($_POST['student_id']) && intval($_POST['student_id']) > 0) {
    $selected_student_id = intval($_POST['student_id']);

    // --- CRITICAL SECURITY CHECK: Verify if the selected student ID belongs to the logged-in parent/user ---
    // This prevents a user from changing the student_id in the form to view another student's data
    foreach ($students as $student) {
        // Compare the submitted student ID with the IDs of students the current user is authorized to see
        if ($student['id'] == $selected_student_id) {
            $is_authorized_student = true;
            $selected_student_name = $student['name']; // Store the authorized student's name
            break; // Found the student in the authorized list
        }
    }

    if (!$is_authorized_student) {
        // If the selected student ID is NOT in the list of students the user is allowed to see, deny access
        die("Access denied: You are not authorized to view the report for this student.");
    }
    // --- END CRITICAL SECURITY CHECK ---

    // If the student is authorized, proceed to fetch their data

    // Fetch student grades
    $sql_grades = "
        SELECT subjects.name AS subject_name, grades.grade
        FROM grades
        JOIN subjects ON grades.subject = subjects.name
        WHERE grades.student_id = ?";

    $stmt_grades = $conn->prepare($sql_grades);

    if ($stmt_grades === false) {
        die("SQL statement preparation failed (Grades): " . $conn->error);
    }

    $stmt_grades->bind_param("i", $selected_student_id);
    $stmt_grades->execute();
    $grades_result = $stmt_grades->get_result();

    if ($grades_result && $grades_result->num_rows > 0) {
        while ($row = $grades_result->fetch_assoc()) {
            $report_card[] = $row;
            // Calculate total grades and count for average
            $total_grades += $row['grade'];
            $grade_count++;
        }
        // Calculate average only if there are grades
        if ($grade_count > 0) {
            $average_grade = $total_grades / $grade_count;
        }
    } else {
        // No report card found is not an error, just means no data for this student
    }
    $stmt_grades->close();

    // Fetch student attendance
    $sql_attendance = "
        SELECT date, status
        FROM attendance
        WHERE student_id = ?
        ORDER BY date DESC";

    $stmt_attendance = $conn->prepare($sql_attendance);

    if ($stmt_attendance === false) {
        die("SQL statement preparation failed (Attendance): " . $conn->error);
    }

    $stmt_attendance->bind_param("i", $selected_student_id);
    $stmt_attendance->execute();
    $attendance_result = $stmt_attendance->get_result();

    if ($attendance_result && $attendance_result->num_rows > 0) {
        while ($row = $attendance_result->fetch_assoc()) {
            $attendance_data[] = $row;
        }
    } else {
         // No attendance found is not an error, just means no data for this student
    }
    $stmt_attendance->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Report Card & Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
            background-image: url('images/background2.png'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            text-align: center;
            color: #5cb85c;
            margin-bottom: 20px;
        }
        .student-name-heading {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
            color: #0056b3;
            font-size: 1.3em;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #5cb85c;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .form-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        form {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        form label {
            font-weight: bold;
            color: #333;
        }
        form select, form button {
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        form select {
            min-width: 200px;
        }
        form button {
            background-color: #5cb85c;
            color: white;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #4cae4c;
        }
        .no-data {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 8px;
            margin-top: 20px;
            color: #666;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .section-divider {
            margin: 30px 0;
            padding: 20px 0;
            border-top: 2px solid #5cb85c;
        }
        .back-link {
            display: inline-block;
            text-align: center;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background-color: #4cae4c;
            text-decoration: none;
        }
        .nav-links {
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script>
        function validateForm() {
            const studentSelect = document.getElementById('student_id');
            const selectedValue = studentSelect.value;

            if (selectedValue === "") {
                alert("Please select a student.");
                return false;  // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Select a Student to Generate Report Card and Attendance</h2>
        <div class="form-section">
            <form method="post" onsubmit="return validateForm();">
            <label for="student_id">Select Student:</label>
            <select name="student_id" id="student_id" required>
                <option value="">-- Choose a student --</option>
                <?php
                // Populating the dropdown with students based on the authorized list ($students array)
                if (!empty($students)) {
                    // Keep the selected student in the dropdown
                    $selected_student_id_from_post = isset($_POST['student_id']) ? intval($_POST['student_id']) : 0;
                    foreach ($students as $student): ?>
                        <option value="<?php echo htmlspecialchars($student['id']); ?>"
                            <?php if ($student['id'] == $selected_student_id_from_post) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($student['name']); ?>
                        </option>
                    <?php endforeach;
                } else {
                    // Message if no students are found for the user (especially relevant for parents with no linked children)
                    echo "<option value='' disabled>No students available</option>";
                }
                ?>
            </select>
            <button type="submit">Generate Report Card & Attendance</button>
        </form>
        </div>

        <?php
        // Only display results if a student has been selected and authorized
        if (isset($_POST['student_id']) && intval($_POST['student_id']) > 0 && $is_authorized_student):
        ?>
            <h3 class="student-name-heading">Report & Attendance for: <?php echo htmlspecialchars($selected_student_name); ?></h3>

            <div class="section-divider"></div> <!-- Add a visual separator -->

            <?php if (!empty($report_card)): ?>
                <h3>Report Card</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_card as $entry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($entry['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($entry['grade']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($grade_count > 0): // Display average only if there were grades ?>
                        <tr>
                            <td><strong>Average</strong></td>
                            <td><?php echo number_format($average_grade, 2); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php else: ?>
                 <div class="no-data">No report card found for this student.</div>
            <?php endif; ?>

            <div class="section-divider"></div> <!-- Add a visual separator -->

            <?php if (!empty($attendance_data)): ?>
                <h3>Attendance Record</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_data as $entry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($entry['date']); ?></td>
                                <td><?php echo htmlspecialchars($entry['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">No attendance record found for this student.</div>
            <?php endif; ?>

        <?php
        // End the conditional block for displaying report/attendance data
        endif;
        ?>
    </div>
    <div class="nav-links">
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>