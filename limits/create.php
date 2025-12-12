<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category'] ?? '');
    $monthlyLimit = floatval($_POST['monthlyLimit'] ?? 0);

    if (empty($category) || $monthlyLimit <= 0) {
        header("Location: list.php?error=invalid_data");
        exit();
    }

    $check = $conn->prepare("SELECT idLimit FROM monthly_limits WHERE idUser = ? AND category = ?");
    $check->bind_param("is", $userId, $category);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        header("Location: list.php?error=limit_exists");
        exit();
    }
    $check->close();

    $stmt = $conn->prepare("INSERT INTO monthly_limits (idUser, category, monthlyLimit) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $userId, $category, $monthlyLimit);
    
    if ($stmt->execute()) {
        header("Location: list.php?message=limit_added");
    } else {
        header("Location: list.php?error=insert_failed");
    }
    $stmt->close();
} else {
    header("Location: list.php");
}

$conn->close();
exit();
?>