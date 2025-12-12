<?php
session_start();
include '../config/database.php';
checkAuth();

$userId = $_SESSION['user_id'];

$limits_result = $conn->query("SELECT * FROM monthly_limits WHERE idUser = $userId ORDER BY category");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Limits - SmartBudget</title>
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
                    <a href="../incomes/list.php" class="text-white hover:text-blue-200">Incomes</a>
                    <a href="../expenses/list.php" class="text-white hover:text-blue-200">Expenses</a>
                    <a href="../cards/list.php" class="text-white hover:text-blue-200">Cards</a>
                    <a href="../transfers/list.php" class="text-white hover:text-blue-200">Transfers</a>
                    <a href="list.php" class="text-white font-bold">Limits</a>
                    <a href="../auth/logout.php" class="text-white hover:text-blue-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Monthly Spending Limits</h1>
                <p class="text-gray-600">Set limits to control your spending by category</p>
            </div>
            <button onclick="showAddForm()" class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition">Add Limit</button>
        </div>

        <div id="addForm" class="hidden bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Set New Monthly Limit</h2>
            <form action="create.php" method="POST" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Category</label>
                        <select name="category" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Category</option>
                            <option value="Food">Food</option>
                            <option value="Transportation">Transportation</option>
                            <option value="Housing">Housing</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Education">Education</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Monthly Limit ($)</label>
                        <input type="number" step="0.01" min="0" name="monthlyLimit" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition">Save Limit</button>
                    <button type="button" onclick="hideAddForm()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Category</th>
                            <th class="px-6 py-3 text-left">Monthly Limit</th>
                            <th class="px-6 py-3 text-left">Spent This Month</th>
                            <th class="px-6 py-3 text-left">Remaining</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $currentMonth = date('Y-m');
                        $limits_data = [];
                        
                        while($limit = $limits_result->fetch_assoc()):
                            $spent_result = $conn->query("
                                SELECT SUM(amountEx) as total 
                                FROM expenses 
                                WHERE idUser = $userId 
                                AND category = '{$limit['category']}'
                                AND DATE_FORMAT(dateEx, '%Y-%m') = '$currentMonth'
                            ");
                            $spent = $spent_result->fetch_assoc()['total'] ?? 0;
                            $remaining = $limit['monthlyLimit'] - $spent;
                            $percentage = $limit['monthlyLimit'] > 0 ? ($spent / $limit['monthlyLimit']) * 100 : 0;

                            if ($percentage >= 100) {
                                $status = "Exceeded";
                                $status_color = "bg-red-100 text-red-800";
                            } elseif ($percentage >= 80) {
                                $status = "Warning";
                                $status_color = "bg-yellow-100 text-yellow-800";
                            } else {
                                $status = "Good";
                                $status_color = "bg-green-100 text-green-800";
                            }
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium"><?php echo htmlspecialchars($limit['category']); ?></td>
                            <td class="px-6 py-4">$<?php echo number_format($limit['monthlyLimit'], 2); ?></td>
                            <td class="px-6 py-4">$<?php echo number_format($spent, 2); ?></td>
                            <td class="px-6 py-4 font-semibold <?php echo $remaining < 0 ? 'text-red-600' : 'text-green-600'; ?>">
                                $<?php echo number_format($remaining, 2); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $status_color; ?>">
                                    <?php echo $status; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="edit.php?id=<?php echo $limit['idLimit']; ?>" class="bg-blue-100 text-blue-600 px-3 py-1 rounded hover:bg-blue-200 transition">Edit</a>
                                    <a href="delete.php?id=<?php echo $limit['idLimit']; ?>" onclick="return confirm('Delete this limit?')" class="bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200 transition">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if($limits_result->num_rows == 0): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <p class="mb-2">No limits set yet.</p>
                                <p>Set monthly limits to control your spending.</p>
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