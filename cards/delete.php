<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?error=no_id");
    exit();
}

$cardId = intval($_GET['id']);

$check = $conn->prepare("SELECT isMain FROM cards WHERE idCard = ? AND idUser = ?");
$check->bind_param("ii", $cardId, $userId);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$card = $result->fetch_assoc();

if ($card['isMain']) {
    header("Location: list.php?error=cannot_delete_main");
    exit();
}

$checkTransactions = $conn->prepare("
    SELECT COUNT(*) as count FROM incomes WHERE idCard = ?
    UNION ALL
    SELECT COUNT(*) as count FROM expenses WHERE idCard = ?
");
$checkTransactions->bind_param("ii", $cardId, $cardId);
$checkTransactions->execute();
$checkTransactions->bind_result($incomeCount, $expenseCount);
$checkTransactions->fetch();
$checkTransactions->close();

if ($incomeCount > 0 || $expenseCount > 0) {
    header("Location: list.php?error=card_has_transactions");
    exit();
}

$stmt = $conn->prepare("DELETE FROM cards WHERE idCard = ? AND idUser = ?");
$stmt->bind_param("ii", $cardId, $userId);

if ($stmt->execute()) {
    header("Location: list.php?message=card_deleted");
} else {
    header("Location: list.php?error=delete_failed");
}

$stmt->close();
$conn->close();
exit();
?>