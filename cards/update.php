<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardId = $_POST['idCard'] ?? '';
    $currentBalance = floatval($_POST['currentBalance'] ?? 0);

    if (empty($cardId)) {
        header("Location: list.php?error=no_id");
        exit();
    }

    $stmt = $conn->prepare("UPDATE cards SET currentBalance = ? WHERE idCard = ? AND idUser = ?");
    $stmt->bind_param("dii", $currentBalance, $cardId, $userId);
    
    if ($stmt->execute()) {
        header("Location: list.php?message=balance_updated");
    } else {
        header("Location: list.php?error=update_failed");
    }
    $stmt->close();
} else {
    header("Location: list.php");
}

$conn->close();
exit();
?>