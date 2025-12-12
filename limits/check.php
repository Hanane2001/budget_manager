<?php
session_start();
include '../config/database.php';
checkAuth();

$userId = $_SESSION['user_id'];
$category = $_GET['category'] ?? '';
$amount = floatval($_GET['amount'] ?? 0);

if (empty($category) || $amount <= 0) {
    echo json_encode(['warning' => false]);
    exit();
}

$currentMonth = date('Y-m');

$limitQuery = $conn->prepare("SELECT monthlyLimit FROM monthly_limits WHERE idUser = ? AND category = ?");
$limitQuery->bind_param("is", $userId, $category);
$limitQuery->execute();
$limitResult = $limitQuery->get_result();

if ($limitResult->num_rows === 0) {
    echo json_encode(['warning' => false]);
    exit();
}

$limit = $limitResult->fetch_assoc();
$limitQuery->close();

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

$warning = ($spent + $amount) > $limit['monthlyLimit'];

echo json_encode([
    'warning' => $warning,
    'spent' => $spent,
    'limit' => $limit['monthlyLimit'],
    'remaining' => $limit['monthlyLimit'] - $spent
]);

$conn->close();
?>