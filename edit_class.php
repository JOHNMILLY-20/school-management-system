<?php include 'session.php'; ?>
<?php
include 'db.php';

$class_id = $_GET['id'];
$result = $conn->query("SELECT * FROM classes WHERE id = $class_id");
$class = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = $_POST['class_name'];
    $grade_level = $_POST['grade_level'];

    $stmt = $conn->prepare("UPDATE classes SET name=?, grade_level=? WHERE id=?");
    $stmt->bind_param("sii", $class_name, $grade_level, $class_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_classes.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Class</title>
</head>
<body>
    <h1>Edit Class</h1>
    <form method="POST" action="">
        <input type="text" name="class_name" value="<?= $class['name']; ?>" required>
        <input type="number" name="grade_level" value="<?= $class['grade_level']; ?>" required>
        <button type="submit">Update Class</button>
    </form>
    <a href="manage_classes.php">Back to Class List</a>
</body>
</html>