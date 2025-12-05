<?php 
include 'config/database.php';

$total_income_result = $conn->query("SELECT SUM(amountIn) as total FROM incomes");
$total_income = $total_income_result->fetch_assoc()['total'] ?? 0;

$total_expense_result = $conn->query("SELECT SUM(amountEx) as total FROM expenses");
$total_expense = $total_expense_result->fetch_assoc()['total'] ?? 0;

$balance = $total_income - $total_expense;

$month_InEx = date('Y-m');
$month_income_result = $conn->query("SELECT SUM(amountIn) as total FROM incomes WHERE DATE_FORMAT(dateIn, '%Y-%m') = '$month_InEx'");
$month_income = $month_income_result->fetch_assoc()['total'] ?? 0;

$month_expense_result = $conn->query("SELECT SUM(amountEx) as total FROM expenses WHERE DATE_FORMAT(dateEx, '%Y-%m') = '$month_InEx'");
$month_expense = $month_expense_result->fetch_assoc()['total'] ?? 0;
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
                <div id="navLinks" class="hidden md:flex space-x-6">
                    <a href="index.php" class="text-white hover:text-blue-200">Home</a>
                    <a href="dashboard.php" class="text-white font-bold">Dashboard</a>
                    <a href="incomes/list.php" class="text-white hover:text-blue-200">Incomes</a>
                    <a href="expenses/list.php" class="text-white hover:text-blue-200">Expenses</a>
                </div>
                <button id="menu_tougle" class="md:hidden text-white"><i class="fas fa-bars text-2xl"></i></button>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Financial Dashboard</h1>

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

        <div class="grid md:grid-cols-2 gap-8 mb-8">
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow p-8 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Add New Income</h3>
                </div>
                <p class="mb-6">Record your latest income source quickly.</p>
                <a href="incomes/list.php" class="inline-block bg-white text-green-600 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">Go to Incomes</a>
            </div>

            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow p-8 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Add New Expense</h3>
                </div>
                <p class="mb-6">Track your spending and manage expenses.</p>
                <a href="expenses/list.php" class="inline-block bg-white text-red-600 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">Go to Expenses</a>
            </div>
        </div>
        <!-- chartjs -->
        <div class="w-full md:w-[70%] lg:w-[50%] mx-auto flex flex-col items-center">
            <h2 class="text-3xl font-bold text-gray-800">Budget Summary</h2>
            <canvas class="mt-5 mb-5 w-full" id="chartjs_bar"></canvas>
        </div>
   
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
                          $recent_incomes = $conn->query("SELECT *, 'Income' as type FROM incomes ORDER BY dateIn DESC LIMIT 3");
                          while($row = $recent_incomes->fetch_assoc()): ?>
                          <tr class="border-b">
                              <td class="px-4 py-3"><?php echo $row['dateIn']; ?></td>
                              <td class="px-4 py-3"><?php echo $row['descriptionIn']; ?></td>
                              <td class="px-4 py-3"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Income</span></td>
                              <td class="px-4 py-3 text-green-600 font-semibold">+$<?php echo number_format($row['amountIn'], 2); ?></td>
                          </tr>
                        <?php endwhile; ?>
                        
                        <?php
                          $recent_expenses = $conn->query("SELECT *, 'Expense' as type FROM expenses ORDER BY dateEx DESC LIMIT 3");
                          while($row = $recent_expenses->fetch_assoc()): ?>
                          <tr class="border-b">
                              <td class="px-4 py-3"><?php echo $row['dateEx']; ?></td>
                              <td class="px-4 py-3"><?php echo $row['descriptionEx']; ?></td>
                              <td class="px-4 py-3"><span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Expense</span></td>
                              <td class="px-4 py-3 text-red-600 font-semibold">-$<?php echo number_format($row['amountEx'], 2); ?></td>
                          </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
    <script src="//code.jquery.com/jquery-1.9.1.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script type="text/javascript">
      var ctx = document.getElementById("chartjs_bar").getContext('2d');
      var gradientGreen = ctx.createLinearGradient(0, 0, 0, 400);
      gradientGreen.addColorStop(0, "#22c55e"); 
      gradientGreen.addColorStop(1, "#16a34a"); 

      var gradientRed = ctx.createLinearGradient(0, 0, 0, 400);
      gradientRed.addColorStop(0, "#ef4444");
      gradientRed.addColorStop(1, "#b91c1c");

                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ["Total Income", "Total Expense"],
                        datasets: [{
                            data: [<?php echo $total_income; ?>, <?php echo $total_expense; ?>],
                            backgroundColor: [gradientGreen,gradientRed]
                        }]
                    },
                    options: {
                      legend: {
                          display: false
                      }
                  }
                });
    </script>
</body>
</html>
<?php closeConnection($conn); ?>