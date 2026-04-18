<?php include 'session.php'; ?>
<?php
include 'db.php';

$student_id = $_GET['id'];
$result = $conn->query("SELECT * FROM students WHERE id = $student_id");
$student = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $class_id = $_POST['class_id'];

    $stmt = $conn->prepare("UPDATE students SET name=?, class_id=? WHERE id=?");
    $stmt->bind_param("sii", $name, $class_id, $student_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_students.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
</head>
<body>
    <h1>Edit Student</h1>
    <form method="POST" action="">
        <input type="text" name="name" value="<?= $student['name']; ?>" required>
        <select name="class_id">
            <?php
            $classes = $conn->query("SELECT * FROM classes");
            while ($row = $classes->fetch_assoc()) {
                $selected = $row['id'] == $student['class_id'] ? 'selected' : '';
                echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
            }
            ?>
        </select>
        <button type="submit">Update Student</button>
    </form>
    <a href="manage_students.php">Back to Student List</a>
</body>
</html>