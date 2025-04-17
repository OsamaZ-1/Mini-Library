<?php include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $imagePath = '';

    // Handle image upload
    if ($_FILES['image']['name']) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir); // Create if not exists

        $filename = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $conn->prepare("INSERT INTO books (title, author, year, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $title, $author, $year, $imagePath);
    $stmt->execute();
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Book</title><link rel="stylesheet" href="styles\index.css"></head>
<body>
    <h1>Add Book</h1>
    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            Title: <input name="title" required><br>
            Author: <input name="author" required><br>
            Year: <input name="year" type="number"><br>
            Cover Image: <input type="file" name="image" accept="image/*"><br>
            <button type="submit">Add</button>
    </form>
    </div>
    <a href="index.php">Back</a>
</body>
</html>
