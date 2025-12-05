<?php
include '../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: list.php?error=no_id");
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM incomes WHERE idIn = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: list.php?error=not_found");
    exit();
}

$income = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Income - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="list.php" class="text-blue-500 hover:text-blue-600"><i class="fas fa-arrow-left mr-2"></i>Back to Incomes</a>
            </div>
            
            <div class="bg-white rounded-xl shadow p-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Income</h1>
                
                <form action="update.php" method="POST">
                    <input type="hidden" name="idIn" value="<?php echo $income['idIn']; ?>">
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-gray-700 mb-2">Amount ($)</label>
                            <input type="number" step="0.01" name="amountIn" required value="<?php echo $income['amountIn']; ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Date</label>
                            <input type="date" name="dateIn" required value="<?php echo $income['dateIn']; ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Description</label>
                            <input type="text" name="descriptionIn" value="<?php echo htmlspecialchars($income['descriptionIn']); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition">Update Income</button>
                            <a href="list.php" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>