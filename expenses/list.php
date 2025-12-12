<?php 
session_start();
include '../config/database.php';
checkAuth();

$userId = $_SESSION['user_id'];

$cards_result = $conn->query("SELECT * FROM cards WHERE idUser = $userId");

$categories = ['Food', 'Transportation', 'Housing', 'Entertainment', 'Shopping', 'Healthcare', 'Education', 'Utilities', 'Other'];

$result = $conn->query("
    SELECT e.*, c.cardName 
    FROM expenses e 
    JOIN cards c ON e.idCard = c.idCard 
    WHERE e.idUser = $userId 
    ORDER BY e.dateEx DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses - SmartBudget</title>
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
                    <a href="../incomes/list.php" class="text-white hover:text-blue-200">Incomes</a>
                    <a href="list.php" class="text-white font-bold">Expenses</a>
                    <a href="../cards/list.php" class="text-white hover:text-blue-200">Cards</a>
                    <a href="../transfers/list.php" class="text-white hover:text-blue-200">Transfers</a>
                    <a href="../limits/list.php" class="text-white hover:text-blue-200">Limits</a>
                    <a href="../auth/logout.php" class="text-white hover:text-blue-200">Logout</a>
                </div>
                <button id="menu_tougle" class="md:hidden text-white"><i class="fas fa-bars text-2xl"></i></button>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div><h1 class="text-3xl font-bold text-gray-800">Expense Management</h1></div>
            <button onclick="showAddForm()" class="bg-red-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-600 transition">Add New Expense</button>
        </div>

        <div id="addForm" class="hidden bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Add New Expense</h2>
            <form action="create.php" method="POST" class="space-y-4" onsubmit="return checkLimit()">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Amount ($)</label>
                        <input type="number" step="0.01" name="amountEx" required id="amountEx" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Date</label>
                        <input type="date" name="dateEx" required value="<?php echo date('Y-m-d'); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Card</label>
                        <select name="idCard" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a card</option>
                            <?php 
                            $cards_result->data_seek(0); 
                            while($card = $cards_result->fetch_assoc()): ?>
                            <option value="<?php echo $card['idCard']; ?>">
                                <?php echo htmlspecialchars($card['cardName']); ?> ($<?php echo number_format($card['currentBalance'], 2); ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Category</label>
                        <select name="category" required id="category" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select category</option>
                            <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 mb-2">Description</label>
                        <input type="text" name="descriptionEx" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="What was this expense for?">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="isRecurring" id="isRecurring" class="mr-2">
                    <label for="isRecurring" class="text-gray-700">Recurring expense (will repeat monthly)</label>
                </div>
                <div id="limitWarning" class="hidden bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                    Warning: This expense may exceed your monthly limit for this category.
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">Save Expense</button>
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
                            <th class="px-6 py-3 text-left">Category</th>
                            <th class="px-6 py-3 text-left">Description</th>
                            <th class="px-6 py-3 text-left">Recurring</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($expense = $result->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4"><?php echo $expense['dateEx']; ?></td>
                                <td class="px-6 py-4 font-semibold text-red-600">$<?php echo number_format($expense['amountEx'], 2); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($expense['cardName']); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                        <?php echo htmlspecialchars($expense['category']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($expense['descriptionEx']); ?></td>
                                <td class="px-6 py-4">
                                    <?php if($expense['isRecurring']): ?>
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Yes</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="edit.php?id=<?php echo $expense['idEx']; ?>" class="bg-blue-100 text-blue-600 px-3 py-1 rounded hover:bg-blue-200 transition">Edit</a>
                                        <a href="delete.php?id=<?php echo $expense['idEx']; ?>" onclick="return confirm('Delete this expense?')" class="bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200 transition">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <p class="mb-2">No expense records found.</p>
                                    <p>Add your first expense to track your spending!</p>
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
        
        async function checkLimit() {
            const category = document.getElementById('category').value;
            const amount = parseFloat(document.getElementById('amountEx').value);
            
            if (!category || !amount) return true;
            
            try {
                const response = await fetch(`../limits/check.php?category=${encodeURIComponent(category)}&amount=${amount}`);
                const data = await response.json();
                
                if (data.warning) {
                    document.getElementById('limitWarning').classList.remove('hidden');
                    return confirm('This expense exceeds your monthly limit. Do you want to proceed anyway?');
                }
            } catch (error) {
                console.error('Error checking limit:', error);
            }
            
            return true;
        }
    </script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
<?php closeConnection($conn); ?>