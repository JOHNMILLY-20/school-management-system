<?php include 'session.php'; ?>
<?php
include 'db.php';

$grade_id = $_GET['id'];
$result = $conn->query("SELECT * FROM grades WHERE id = $grade_id");
$grade = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $subject = $_POST['subject'];
    $grade_value = $_POST['grade'];

    $stmt = $conn->prepare("UPDATE grades SET student_id=?, subject=?, grade=? WHERE id=?");
    $stmt->bind_param("isii", $student_id, $subject, $grade_value, $grade_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_grades.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Grade</title>
</head>
<body>
    <h1>Edit Grade</h1>
    <form method="POST" action="">
        <select name="student_id">
            <?php
            $students = $conn->query("SELECT * FROM students");
            while ($row = $students->fetch_assoc()) {
                $selected = $row['id'] == $grade['student_id'] ? 'selected' : '';
                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
            }
            ?>
        </select>
        <input type="text" name="subject" value="<?= $grade['subject']; ?>" required>
        <input type="number" name="grade" value="<?= $grade['grade']; ?>" step="0.1" required>
        <button type="submit">Update Grade</button>
    </form>
    <a href="manage_grades.php">Back to Grade List</a>
</body>
</html>