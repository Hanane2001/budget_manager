<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amountIn'] ?? 0);
    $date = $_POST['dateIn'] ?? '';
    $description = trim($_POST['descriptionIn'] ?? '');
    $idCard = intval($_POST['idCard'] ?? 0);
    $isRecurring = isset($_POST['isRecurring']) ? 1 : 0;

    if ($amount <= 0 || empty($date) || $idCard <= 0) {
        header("Location: list.php?error=missing_fields");
        exit();
    }

    $checkCard = $conn->prepare("SELECT idCard FROM cards WHERE idCard = ? AND idUser = ?");
    $checkCard->bind_param("ii", $idCard, $userId);
    $checkCard->execute();
    $cardResult = $checkCard->get_result();
    
    if ($cardResult->num_rows === 0) {
        header("Location: list.php?error=invalid_card");
        exit();
    }
    $checkCard->close();

    $stmt = $conn->prepare("INSERT INTO incomes (idUser, idCard, amountIn, dateIn, descriptionIn, isRecurring) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidssi", $userId, $idCard, $amount, $date, $description, $isRecurring);
    
    if ($stmt->execute()) {
        $conn->query("UPDATE cards SET currentBalance = currentBalance + $amount WHERE idCard = $idCard");
        
        header("Location: list.php?message=income_added");
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