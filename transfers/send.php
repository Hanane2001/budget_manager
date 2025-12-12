<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient = trim($_POST['recipient'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $sender_card_id = intval($_POST['sender_card_id'] ?? 0);
    $description = trim($_POST['descriptionTrans'] ?? '');

    if (empty($recipient) || $amount <= 0 || $sender_card_id <= 0) {
        header("Location: list.php?error=missing_fields");
        exit();
    }

    $checkCard = $conn->prepare("SELECT currentBalance FROM cards WHERE idCard = ? AND idUser = ?");
    $checkCard->bind_param("ii", $sender_card_id, $userId);
    $checkCard->execute();
    $cardResult = $checkCard->get_result();
    
    if ($cardResult->num_rows === 0) {
        header("Location: list.php?error=invalid_card");
        exit();
    }
    
    $card = $cardResult->fetch_assoc();
    $checkCard->close();
    
    if ($amount > $card['currentBalance']) {
        header("Location: list.php?error=insufficient_funds");
        exit();
    }

    if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
        $findUser = $conn->prepare("SELECT idUser FROM users WHERE email = ? AND idUser != ?");
        $findUser->bind_param("si", $recipient, $userId);
    } else {
        $findUser = $conn->prepare("SELECT idUser FROM users WHERE idUser = ? AND idUser != ?");
        $recipientId = intval($recipient);
        $findUser->bind_param("ii", $recipientId, $userId);
    }
    
    $findUser->execute();
    $userResult = $findUser->get_result();
    
    if ($userResult->num_rows === 0) {
        header("Location: list.php?error=user_not_found");
        exit();
    }
    
    $receiver = $userResult->fetch_assoc();
    $receiver_id = $receiver['idUser'];
    $findUser->close();

    $mainCardQuery = $conn->prepare("SELECT idCard FROM cards WHERE idUser = ? AND isMain = 1 LIMIT 1");
    $mainCardQuery->bind_param("i", $receiver_id);
    $mainCardQuery->execute();
    $mainCardResult = $mainCardQuery->get_result();
    
    if ($mainCardResult->num_rows === 0) {
        header("Location: list.php?error=receiver_no_main_card");
        exit();
    }
    
    $receiver_card = $mainCardResult->fetch_assoc();
    $receiver_card_id = $receiver_card['idCard'];
    $mainCardQuery->close();

    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO transfers (sender_id, receiver_id, amount, sender_card_id, receiver_card_id, descriptionTrans, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'completed')
        ");
        $stmt->bind_param("iidiis", $userId, $receiver_id, $amount, $sender_card_id, $receiver_card_id, $description);
        $stmt->execute();

        $conn->query("UPDATE cards SET currentBalance = currentBalance - $amount WHERE idCard = $sender_card_id");

        $conn->query("UPDATE cards SET currentBalance = currentBalance + $amount WHERE idCard = $receiver_card_id");
        
        $conn->commit();
        header("Location: list.php?message=transfer_sent");
        
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: list.php?error=transfer_failed");
    }
    
    $stmt->close();
} else {
    header("Location: list.php");
}

$conn->close();
exit();
?>