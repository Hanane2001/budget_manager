<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amountEx'] ?? 0);
    $date = $_POST['dateEx'] ?? '';
    $description = trim($_POST['descriptionEx'] ?? '');
    $idCard = intval($_POST['idCard'] ?? 0);
    $category = trim($_POST['category'] ?? 'Other');
    $isRecurring = isset($_POST['isRecurring']) ? 1 : 0;

    if ($amount <= 0 || empty($date) || $idCard <= 0 || empty($category)) {
        header("Location: list.php?error=missing_fields");
        exit();
    }

    $checkCard = $conn->prepare("SELECT currentBalance FROM cards WHERE idCard = ? AND idUser = ?");
    $checkCard->bind_param("ii", $idCard, $userId);
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

    $currentMonth = date('Y-m');
    $limitCheck = $conn->prepare("SELECT monthlyLimit FROM monthly_limits WHERE idUser = ? AND category = ?");
    $limitCheck->bind_param("is", $userId, $category);
    $limitCheck->execute();
    $limitResult = $limitCheck->get_result();
    
    if ($limitResult->num_rows > 0) {
        $limit = $limitResult->fetch_assoc();
        $monthlyLimit = $limit['monthlyLimit'];

        $spentQuery = $conn->prepare("
            SELECT SUM(amountEx) as total 
            FROM expenses 
            WHERE idUser = ? 
            AND category = ? 
            AND DATE_FORMAT(dateEx, '%Y-%m') = ?
        ");
        $spentQuery->bind_param("iss", $userId, $category, $currentMonth);
        $spentQuery->execute();
        $spentResult = $spentQuery->get_result();
        $spent = $spentResult->fetch_assoc()['total'] ?? 0;
        $spentQuery->close();
        
        if (($spent + $amount) > $monthlyLimit) {
            header("Location: list.php?error=limit_exceeded");
            exit();
        }
    }
    $limitCheck->close();

    $stmt = $conn->prepare("INSERT INTO expenses (idUser, idCard, amountEx, dateEx, descriptionEx, category, isRecurring) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidsssi", $userId, $idCard, $amount, $date, $description, $category, $isRecurring);
    
    if ($stmt->execute()) {
        $conn->query("UPDATE cards SET currentBalance = currentBalance - $amount WHERE idCard = $idCard");
        
        header("Location: list.php?message=expense_added");
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