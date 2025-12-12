<?php
session_start();
include '../config/database.php';
checkAuth();

$userId = $_SESSION['user_id'];

$cards_result = $conn->query("SELECT * FROM cards WHERE idUser = $userId ORDER BY isMain DESC, created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cards - SmartBudget</title>
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
                    <a href="../expenses/list.php" class="text-white hover:text-blue-200">Expenses</a>
                    <a href="list.php" class="text-white font-bold">Cards</a>
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
            <div>
                <h1 class="text-3xl font-bold text-gray-800">My Cards</h1>
                <p class="text-gray-600">Manage your bank cards</p>
            </div>
            <button onclick="showAddForm()" class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">Add New Card</button>
        </div>

        <div id="addForm" class="hidden bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Add New Card</h2>
            <form action="create.php" method="POST" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Card Name</label>
                        <input type="text" name="cardName" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Main Card">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Bank Name</label>
                        <input type="text" name="bankName" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Banque Populaire">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Card Number</label>
                        <input type="text" name="cardNumber" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Optional">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Initial Balance ($)</label>
                        <input type="number" step="0.01" name="currentBalance" value="0.00" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="isMain" id="isMain" class="mr-2">
                    <label for="isMain" class="text-gray-700">Set as main card (for receiving transfers)</label>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">Save Card</button>
                    <button type="button" onclick="hideAddForm()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                </div>
            </form>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($cards_result->num_rows > 0): ?>
                <?php while($card = $cards_result->fetch_assoc()): ?>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg text-white p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($card['cardName']); ?></h3>
                            <p class="text-blue-100"><?php echo htmlspecialchars($card['bankName']); ?></p>
                            <?php if($card['isMain']): ?>
                                <span class="inline-block bg-yellow-500 text-white text-xs px-2 py-1 rounded-full mt-2">Main Card</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-blue-100 text-sm">Current Balance</p>
                        <p class="text-3xl font-bold">$<?php echo number_format($card['currentBalance'], 2); ?></p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="edit.php?id=<?php echo $card['idCard']; ?>" class="flex-1 bg-white text-blue-600 text-center py-2 rounded-lg hover:bg-blue-50 transition">Edit</a>
                        <?php if(!$card['isMain']): ?>
                        <a href="delete.php?id=<?php echo $card['idCard']; ?>" onclick="return confirm('Delete this card?')" class="flex-1 bg-red-500 text-white text-center py-2 rounded-lg hover:bg-red-600 transition">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-3 bg-white rounded-xl shadow p-8 text-center">
                    <i class="fas fa-credit-card text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No Cards Yet</h3>
                    <p class="text-gray-600 mb-4">Add your first card to start tracking your finances.</p>
                    <button onclick="showAddForm()" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">Add Your First Card</button>
                </div>
            <?php endif; ?>
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