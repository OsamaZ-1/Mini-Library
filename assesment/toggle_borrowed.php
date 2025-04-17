<?php
include 'db.php';

$id = (int)$_POST['id'];
$borrowed = (int)$_POST['borrowed'];

$stmt = $conn->prepare("UPDATE books SET borrowed=? WHERE id=?");
$stmt->bind_param("ii", $borrowed, $id);
$stmt->execute();
?>
