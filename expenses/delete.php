<?php
include '../config/database.php';
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?error=no_id");
    exit();
}

$id = intval($_GET['id']);

$sql = "DELETE FROM expenses WHERE idEx = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: list.php?message=expense_deleted");
} else {
    header("Location: list.php?error=delete_failed");
}

$stmt->close();
$conn->close();
exit();
?>