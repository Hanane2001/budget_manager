<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?error=no_id");
    exit();
}

$limitId = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM monthly_limits WHERE idLimit = ? AND idUser = ?");
$stmt->bind_param("ii", $limitId, $userId);

if ($stmt->execute()) {
    header("Location: list.php?message=limit_deleted");
} else {
    header("Location: list.php?error=delete_failed");
}

$stmt->close();
$conn->close();
exit();
?>