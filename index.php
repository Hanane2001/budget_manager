<?php 
session_start();
include 'config/database.php'; 

// if (isset($_SESSION['user_id'])) {
//     header("Location: dashboard.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartBudget - Personal Finance Manager</title>
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
                    <a href="index.php" class="text-white hover:text-blue-200">Home</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="text-white hover:text-blue-200">Dashboard</a>
                        <a href="incomes/list.php" class="text-white hover:text-blue-200">Incomes</a>
                        <a href="expenses/list.php" class="text-white hover:text-blue-200">Expenses</a>
                        <a href="cards/list.php" class="text-white hover:text-blue-200">Cards</a>
                        <a href="transfers/list.php" class="text-white hover:text-blue-200">Transfers</a>
                        <a href="limits/list.php" class="text-white hover:text-blue-200">Limits</a>
                        <a href="auth/logout.php" class="text-white hover:text-blue-200">Logout</a>
                    <?php else: ?>
                        <a href="auth/login.php" class="text-white hover:text-blue-200">Login</a>
                        <a href="auth/register.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 transition">Register</a>
                    <?php endif; ?>
                </div>
                <button id="menu_tougle" class="md:hidden text-white"><i class="fas fa-bars text-2xl"></i></button>
            </div>
        </div>
    </nav>

    <section class="py-20 bg-gradient-to-r from-blue-500 to-purple-600 min-h-screen">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold text-white mb-6">Take Control of Your Finances</h1>
            <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">
                Track your income, manage expenses, set limits, and achieve your financial goals with SmartBudget.
                Simple, intuitive, and completely free.
            </p>
            <div class="flex justify-center space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">View Dashboard</a>
                    <a href="incomes/list.php" class="bg-blue-800 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-900 transition">Add Income</a>
                <?php else: ?>
                    <a href="auth/login.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">Login</a>
                    <a href="auth/register.php" class="bg-blue-800 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-900 transition">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <script src="assets/js/main.js"></script>
</body>
</html>
<?php closeConnection($conn); ?>