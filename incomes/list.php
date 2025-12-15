<?php 
include '../config/database.php';

$result = $conn->query("SELECT * FROM incomes ORDER BY dateIn DESC");
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
                <div id="navLinks" class="hidden md:flex space-x-6">
                    <a href="../index.php" class="text-white hover:text-blue-200">Home</a>
                    <a href="../dashboard.php" class="text-white hover:text-blue-200">Dashboard</a>
                    <a href="list.php" class="text-white font-bold">Incomes</a>
                    <a href="../expenses/list.php" class="text-white hover:text-blue-200">Expenses</a>
                </div>
                <button id="menu_tougle" class="md:hidden text-white"><i class="fas fa-bars text-2xl"></i></button>
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
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Amount ($)</label>
                        <input type="number" step="0.01" name="amountIn" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Date</label>
                        <input type="date" name="dateIn" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Description</label>
                        <input type="text" name="descriptionIn" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex space-x-3">
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
                            <th class="px-6 py-3 text-left">ID</th>
                            <th class="px-6 py-3 text-left">Amount</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Description</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($income = $result->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4"><?php echo $income['idIn']; ?></td>
                                <td class="px-6 py-4 font-semibold text-green-600">$<?php echo number_format($income['amountIn'], 2); ?></td>
                                <td class="px-6 py-4"><?php echo $income['dateIn']; ?></td>
                                <td class="px-6 py-4"><?php echo $income['descriptionIn']; ?></td>
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
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500"><p>No income records found. Add your first income!</p></td>
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