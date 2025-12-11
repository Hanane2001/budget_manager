<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardName = trim($_POST['cardName'] ?? '');
    $bankName = trim($_POST['bankName'] ?? '');
    $cardNumber = trim($_POST['cardNumber'] ?? '');
    $currentBalance = floatval($_POST['currentBalance'] ?? 0);
    $isMain = isset($_POST['isMain']) ? 1 : 0;

    if (empty($cardName) || empty($bankName)) {
        header("Location: list.php?error=missing_fields");
        exit();
    }

    if ($isMain) {
        $conn->query("UPDATE cards SET isMain = 0 WHERE idUser = $userId");
    }

    $stmt = $conn->prepare("INSERT INTO cards (idUser, cardName, bankName, cardNumber, currentBalance, isMain) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdi", $userId, $cardName, $bankName, $cardNumber, $currentBalance, $isMain);
    
    if ($stmt->execute()) {
        header("Location: list.php?message=card_added");
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