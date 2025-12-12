<?php
session_start();
include '../config/database.php';
$userId = checkAuth();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?error=no_id");
    exit();
}

$limitId = intval($_GET['id']);

$check = $conn->prepare("SELECT * FROM monthly_limits WHERE idLimit = ? AND idUser = ?");
$check->bind_param("ii", $limitId, $userId);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$limit = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monthlyLimit = floatval($_POST['monthlyLimit'] ?? 0);

    if ($monthlyLimit <= 0) {
        header("Location: list.php?error=invalid_limit");
        exit();
    }

    $stmt = $conn->prepare("UPDATE monthly_limits SET monthlyLimit = ? WHERE idLimit = ? AND idUser = ?");
    $stmt->bind_param("dii", $monthlyLimit, $limitId, $userId);
    
    if ($stmt->execute()) {
        header("Location: list.php?message=limit_updated");
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
    <title>Edit Limit - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <div class="mb-6">
                <a href="list.php" class="text-blue-500 hover:text-blue-600">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Limits
                </a>
            </div>
            
            <div class="bg-white rounded-xl shadow p-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Limit</h1>
                
                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 mb-2">Category</label>
                        <input type="text" value="<?php echo htmlspecialchars($limit['category']); ?>" readonly class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Monthly Limit ($)</label>
                        <input type="number" step="0.01" min="0" name="monthlyLimit" required value="<?php echo $limit['monthlyLimit']; ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex space-x-3 pt-4">
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">Update Limit</button>
                        <a href="list.php" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>