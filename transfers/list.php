<?php
session_start();
include '../config/database.php';
checkAuth();

$userId = $_SESSION['user_id'];

$sent_transfers = $conn->query("
    SELECT t.*, u.email as receiver_email, c.cardName as sender_card 
    FROM transfers t
    JOIN users u ON t.receiver_id = u.idUser
    JOIN cards c ON t.sender_card_id = c.idCard
    WHERE t.sender_id = $userId
    ORDER BY t.created_at DESC
");

$received_transfers = $conn->query("
    SELECT t.*, u.email as sender_email, c.cardName as receiver_card 
    FROM transfers t
    JOIN users u ON t.sender_id = u.idUser
    JOIN cards c ON t.receiver_card_id = c.idCard
    WHERE t.receiver_id = $userId
    ORDER BY t.created_at DESC
");

$cards_result = $conn->query("SELECT * FROM cards WHERE idUser = $userId");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfers - SmartBudget</title>
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
                    <a href="../cards/list.php" class="text-white hover:text-blue-200">Cards</a>
                    <a href="list.php" class="text-white font-bold">Transfers</a>
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
                <h1 class="text-3xl font-bold text-gray-800">Money Transfers</h1>
                <p class="text-gray-600">Send and receive money from other users</p>
            </div>
            <button onclick="showSendForm()" class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">Send Money</button>
        </div>

        <div id="sendForm" class="hidden bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Send Money</h2>
            <form action="send.php" method="POST" class="space-y-4" onsubmit="return validateTransfer()">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Recipient</label>
                        <div class="flex space-x-2">
                            <select id="searchType" onchange="toggleSearch()" class="px-3 py-2 border rounded-lg">
                                <option value="email">By Email</option>
                                <option value="id">By User ID</option>
                            </select>
                            <input type="text" id="recipientInput" name="recipient" required placeholder="Enter email or user ID" class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Amount ($)</label>
                        <input type="number" step="0.01" min="0.01" id="amount" name="amount" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">From Card</label>
                        <select name="sender_card_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a card</option>
                            <?php while($card = $cards_result->fetch_assoc()): ?>
                            <option value="<?php echo $card['idCard']; ?>" data-balance="<?php echo $card['currentBalance']; ?>">
                                <?php echo htmlspecialchars($card['cardName']); ?> ($<?php echo number_format($card['currentBalance'], 2); ?>)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Description</label>
                        <input type="text" name="descriptionTrans" placeholder="Optional" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">Send Money</button>
                    <button type="button" onclick="hideSendForm()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Sent Transfers</h2>
            <?php if ($sent_transfers->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">To</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">From Card</th>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($transfer = $sent_transfers->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3"><?php echo date('Y-m-d', strtotime($transfer['created_at'])); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($transfer['receiver_email']); ?></td>
                            <td class="px-4 py-3 font-semibold text-red-600">-$<?php echo number_format($transfer['amount'], 2); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($transfer['sender_card']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($transfer['descriptionTrans'] ?? '-'); ?></td>
                            <td class="px-4 py-3">
                                <?php if($transfer['status'] == 'completed'): ?>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                                <?php elseif($transfer['status'] == 'pending'): ?>
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Failed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center py-4">No sent transfers yet.</p>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Received Transfers</h2>
            <?php if ($received_transfers->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">From</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">To Card</th>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($transfer = $received_transfers->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3"><?php echo date('Y-m-d', strtotime($transfer['created_at'])); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($transfer['sender_email']); ?></td>
                            <td class="px-4 py-3 font-semibold text-green-600">+$<?php echo number_format($transfer['amount'], 2); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($transfer['receiver_card']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($transfer['descriptionTrans'] ?? '-'); ?></td>
                            <td class="px-4 py-3">
                                <?php if($transfer['status'] == 'completed'): ?>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                                <?php elseif($transfer['status'] == 'pending'): ?>
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Failed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-center py-4">No received transfers yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showSendForm() {
            document.getElementById('sendForm').classList.remove('hidden');
        }
        
        function hideSendForm() {
            document.getElementById('sendForm').classList.add('hidden');
        }
        
        function toggleSearch() {
            const searchType = document.getElementById('searchType').value;
            const recipientInput = document.getElementById('recipientInput');
            recipientInput.placeholder = searchType === 'email' ? 'Enter email address' : 'Enter user ID';
        }
        
        function validateTransfer() {
            const amount = parseFloat(document.getElementById('amount').value);
            const cardSelect = document.querySelector('select[name="sender_card_id"]');
            const selectedOption = cardSelect.options[cardSelect.selectedIndex];
            const cardBalance = parseFloat(selectedOption.getAttribute('data-balance'));
            
            if (amount > cardBalance) {
                alert('Insufficient funds on selected card!');
                return false;
            }
            
            return true;
        }
    </script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
<?php closeConnection($conn); ?>