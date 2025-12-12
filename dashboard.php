<?php 
session_start();
include 'config/database.php';
checkAuth();

$userId = $_SESSION['user_id'];

$total_income_result = $conn->query("SELECT SUM(amountIn) as total FROM incomes WHERE idUser = $userId");
$total_income = $total_income_result->fetch_assoc()['total'] ?? 0;

$total_expense_result = $conn->query("SELECT SUM(amountEx) as total FROM expenses WHERE idUser = $userId");
$total_expense = $total_expense_result->fetch_assoc()['total'] ?? 0;

$balance = $total_income - $total_expense;

$month_InEx = date('Y-m');
$month_income_result = $conn->query("SELECT SUM(amountIn) as total FROM incomes WHERE idUser = $userId AND DATE_FORMAT(dateIn, '%Y-%m') = '$month_InEx'");
$month_income = $month_income_result->fetch_assoc()['total'] ?? 0;

$month_expense_result = $conn->query("SELECT SUM(amountEx) as total FROM expenses WHERE idUser = $userId AND DATE_FORMAT(dateEx, '%Y-%m') = '$month_InEx'");
$month_expense = $month_expense_result->fetch_assoc()['total'] ?? 0;

$main_card_result = $conn->query("SELECT * FROM cards WHERE idUser = $userId AND isMain = 1 LIMIT 1");
$main_card = $main_card_result->fetch_assoc();

$limits_result = $conn->query("SELECT * FROM monthly_limits WHERE idUser = $userId");
$limits = [];
while($row = $limits_result->fetch_assoc()) {
    $limits[$row['category']] = $row['monthlyLimit'];
}

$category_expenses = [];
$cat_expenses_result = $conn->query("SELECT category, SUM(amountEx) as total FROM expenses WHERE idUser = $userId AND DATE_FORMAT(dateEx, '%Y-%m') = '$month_InEx' GROUP BY category");
while($row = $cat_expenses_result->fetch_assoc()) {
    $category_expenses[$row['category']] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SmartBudget</title>
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
                    <a href="index.php" class="text-white hover:text-blue-200">Home</a>
                    <a href="dashboard.php" class="text-white font-bold">Dashboard</a>
                    <a href="incomes/list.php" class="text-white hover:text-blue-200">Incomes</a>
                    <a href="expenses/list.php" class="text-white hover:text-blue-200">Expenses</a>
                    <a href="cards/list.php" class="text-white hover:text-blue-200">Cards</a>
                    <a href="transfers/list.php" class="text-white hover:text-blue-200">Transfers</a>
                    <a href="limits/list.php" class="text-white hover:text-blue-200">Limits</a>
                    <a href="auth/logout.php" class="text-white hover:text-blue-200">Logout</a>
                </div>
                <div class="text-white">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?>!</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Financial Dashboard</h1>
        <p class="text-gray-600 mb-8">Overview of your financial situation</p>

        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Incomes</p>
                        <h3 class="text-2xl font-bold text-gray-800">$<?php echo number_format($total_income, 2); ?></h3>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Expenses</p>
                        <h3 class="text-2xl font-bold text-gray-800">$<?php echo number_format($total_expense, 2); ?></h3>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Current Balance</p>
                        <h3 class="text-2xl font-bold <?php echo $balance >= 0 ? 'text-green-600' : 'text-red-600'; ?>">$<?php echo number_format($balance, 2); ?></h3>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">This Month</p>
                        <h3 class="text-xl font-bold text-gray-800">+$<?php echo number_format($month_income, 2); ?> / -$<?php echo number_format($month_expense, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <?php if ($main_card): ?>
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Main Card</h3>
                </div>
                <p class="text-xl font-bold mb-2">$<?php echo number_format($main_card['currentBalance'], 2); ?></p>
                <p class="text-blue-100"><?php echo $main_card['bankName']; ?> - <?php echo $main_card['cardName']; ?></p>
            </div>
            <?php endif; ?>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Add Income</h3>
                </div>
                <p class="mb-4">Record your latest income source quickly.</p>
                <a href="incomes/list.php" class="inline-block bg-white text-green-600 px-4 py-2 rounded-lg font-semibold hover:bg-green-50 transition">Go to Incomes</a>
            </div>

            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Add Expense</h3>
                </div>
                <p class="mb-4">Track your spending and manage expenses.</p>
                <a href="expenses/list.php" class="inline-block bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition">Go to Expenses</a>
            </div>
        </div>

        <?php if (!empty($limits)): ?>
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Monthly Limits Status</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach($limits as $category => $limit): 
                    $spent = $category_expenses[$category] ?? 0;
                    $percentage = $limit > 0 ? min(100, ($spent / $limit) * 100) : 0;
                    $color = $percentage >= 100 ? 'bg-red-500' : ($percentage >= 80 ? 'bg-yellow-500' : 'bg-green-500');
                ?>
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between mb-2">
                        <span class="font-semibold"><?php echo htmlspecialchars($category); ?></span>
                        <span class="<?php echo $percentage >= 100 ? 'text-red-600' : 'text-gray-600'; ?>">
                            $<?php echo number_format($spent, 2); ?> / $<?php echo number_format($limit, 2); ?>
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full <?php echo $color; ?>" style="width: <?php echo $percentage; ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Transactions</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_transactions = $conn->query("
                            (SELECT idIn as id, dateIn as date, descriptionIn as description, amountIn as amount, 'Income' as type 
                             FROM incomes WHERE idUser = $userId 
                             ORDER BY dateIn DESC LIMIT 3)
                            UNION ALL
                            (SELECT idEx as id, dateEx as date, descriptionEx as description, amountEx as amount, 'Expense' as type 
                             FROM expenses WHERE idUser = $userId 
                             ORDER BY dateEx DESC LIMIT 3)
                            ORDER BY date DESC LIMIT 6
                        ");
                        
                        while($row = $recent_transactions->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3"><?php echo $row['date']; ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($row['description']); ?></td>
                                <td class="px-4 py-3">
                                    <?php if($row['type'] == 'Income'): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Income</span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Expense</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 font-semibold <?php echo $row['type'] == 'Income' ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $row['type'] == 'Income' ? '+' : '-'; ?>$<?php echo number_format($row['amount'], 2); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        
                        <?php if($recent_transactions->num_rows == 0): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No transactions yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>
<?php closeConnection($conn); ?>