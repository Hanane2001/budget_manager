<?php 
session_start();
include '../config/database.php';
checkAuth();

$userId = $_SESSION['user_id'];

$cards_result = $conn->query("SELECT * FROM cards WHERE idUser = $userId");

$result = $conn->query("
    SELECT i.*, c.cardName 
    FROM incomes i 
    JOIN cards c ON i.idCard = c.idCard 
    WHERE i.idUser = $userId 
    ORDER BY i.dateIn DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incomes - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <nav class="bg-blue-600 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-wallet text-white text-2xl"></i>
                    <span class="text-white text-xl font-bold">SmartBudget</span>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="../index.php" class="text-white hover:text-blue-200">Home</a>
                    <a href="../dashboard.php" class="text-white hover:text-blue-200">Dashboard</a>
                    <a href="list.php" class="text-white font-bold">Incomes</a>
                    <a href="../expenses/list.php" class="text-white hover:text-blue-200">Expenses</a>
                    <a href="../cards/list.php" class="text-white hover:text-blue-200">Cards</a>
                    <a href="../transfers/list.php" class="text-white hover:text-blue-200">Transfers</a>
                    <a href="../limits/list.php" class="text-white hover:text-blue-200">Limits</a>
                    <a href="../auth/logout.php" class="text-white hover:text-blue-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div><h1 class="text-3xl font-bold text-gray-800">Income Management</h1></div>
            <button onclick="showAddForm()" class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition">Add New Income</button>
        </div>

        <div id="addForm" class="hidden bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Add New Income</h2>
            <form action="create.php" method="POST" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Amount ($)</label>
                        <input type="number" step="0.01" name="amountIn" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Date</label>
                        <input type="date" name="dateIn" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Card</label>
                        <select name="idCard" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a card</option>
                            <?php while($card = $cards_result->fetch_assoc()): ?>
                            <option value="<?php echo $card['idCard']; ?>">
                                <?php echo htmlspecialchars($card['cardName']); ?> (<?php echo htmlspecialchars($card['bankName']); ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Description</label>
                        <input type="text" name="descriptionIn" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Salary, Freelance, etc.">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="isRecurring" id="isRecurring" class="mr-2">
                    <label for="isRecurring" class="text-gray-700">Recurring income (will repeat monthly)</label>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">Save Income</button>
                    <button type="button" onclick="hideAddForm()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Amount</th>
                            <th class="px-6 py-3 text-left">Card</th>
                            <th class="px-6 py-3 text-left">Description</th>
                            <th class="px-6 py-3 text-left">Recurring</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($income = $result->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4"><?php echo $income['dateIn']; ?></td>
                                <td class="px-6 py-4 font-semibold text-green-600">$<?php echo number_format($income['amountIn'], 2); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($income['cardName']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($income['descriptionIn']); ?></td>
                                <td class="px-6 py-4">
                                    <?php if($income['isRecurring']): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Yes</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="edit.php?id=<?php echo $income['idIn']; ?>" class="bg-blue-100 text-blue-600 px-3 py-1 rounded hover:bg-blue-200 transition">Edit</a>
                                        <a href="delete.php?id=<?php echo $income['idIn']; ?>" onclick="return confirm('Delete this income?')" class="bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200 transition">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <p class="mb-2">No income records found.</p>
                                    <p>Add your first income to get started!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function showAddForm() {
            document.getElementById('addForm').classList.remove('hidden');
        }
        
        function hideAddForm() {
            document.getElementById('addForm').classList.add('hidden');
        }
    </script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
<?php closeConnection($conn); ?>