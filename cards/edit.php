<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?error=no_id");
    exit();
}

$cardId = intval($_GET['id']);

$check = $conn->prepare("SELECT * FROM cards WHERE idCard = ? AND idUser = ?");
$check->bind_param("ii", $cardId, $userId);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$card = $result->fetch_assoc();

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

    if ($isMain && !$card['isMain']) {
        $conn->query("UPDATE cards SET isMain = 0 WHERE idUser = $userId");
    }

    $stmt = $conn->prepare("UPDATE cards SET cardName = ?, bankName = ?, cardNumber = ?, currentBalance = ?, isMain = ? WHERE idCard = ? AND idUser = ?");
    $stmt->bind_param("sssdiii", $cardName, $bankName, $cardNumber, $currentBalance, $isMain, $cardId, $userId);
    
    if ($stmt->execute()) {
        header("Location: list.php?message=card_updated");
    } else {
        header("Location: list.php?error=update_failed");
    }
    $stmt->close();
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Card - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="list.php" class="text-blue-500 hover:text-blue-600">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Cards
                </a>
            </div>
            
            <div class="bg-white rounded-xl shadow p-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Card</h1>
                
                <form action="" method="POST" class="space-y-4">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Card Name</label>
                            <input type="text" name="cardName" required value="<?php echo htmlspecialchars($card['cardName']); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Bank Name</label>
                            <input type="text" name="bankName" required value="<?php echo htmlspecialchars($card['bankName']); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Card Number</label>
                            <input type="text" name="cardNumber" value="<?php echo htmlspecialchars($card['cardNumber']); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Current Balance ($)</label>
                            <input type="number" step="0.01" name="currentBalance" value="<?php echo $card['currentBalance']; ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="isMain" id="isMain" <?php echo $card['isMain'] ? 'checked' : ''; ?> class="mr-2">
                        <label for="isMain" class="text-gray-700">Set as main card (for receiving transfers)</label>
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">Update Card</button>
                        <a href="list.php" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>