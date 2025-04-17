<?php
include 'db.php';
$id = $_GET['id'];

// Get existing book
$result = $conn->query("SELECT * FROM books WHERE id=$id");
$book = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title  = $_POST['title'];
    $author = $_POST['author'];
    $year   = $_POST['year'];
    $imagePath = $book['image_path']; // Keep old image by default

    // Check if new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);

        $filename = basename($_FILES["image"]["name"]);
        $newPath = $targetDir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $newPath)) {
            $imagePath = $newPath;

            // Optional: delete old image if it exists and is not blank
            if (!empty($book['image_path']) && file_exists($book['image_path'])) {
                unlink($book['image_path']);
            }
        }
    }

    // Update query
    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, year=?, image_path=? WHERE id=?");
    $stmt->bind_param("ssisi", $title, $author, $year, $imagePath, $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
<html>
<head><title>Edit Book</title><link rel="stylesheet" href="styles\index.css"></head>
<body>
    <h1>Edit Book</h1>
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            Title: <input name="title" value="<?= htmlspecialchars($book['title']) ?>" required><br>
            Author: <input name="author" value="<?= htmlspecialchars($book['author']) ?>" required><br>
            Year: <input name="year" type="number" value="<?= $book['year'] ?>"><br>
            Current Image:<br>
            <?php if ($book['image_path']): ?>
                <img src="<?= $book['image_path'] ?>" style="width:100px;"><br>
            <?php else: ?>
                <p>No image uploaded.</p>
            <?php endif; ?>

            Change Image: <input type="file" name="image" accept="image/*"><br>
                <button type="submit">Update</button>
        </form>
        </div>
    <a href="index.php">Back</a>
</body>
</html>
