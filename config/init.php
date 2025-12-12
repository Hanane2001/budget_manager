<?php
function initRecurringTransactions() {
    global $conn;
    
    if (date('d') != '01') {
        return;
    }
    
    $currentMonth = date('Y-m');
    $lastMonth = date('Y-m', strtotime('-1 month'));

    $check = $conn->query("SELECT COUNT(*) as count FROM incomes WHERE isRecurring = 1 AND DATE_FORMAT(dateIn, '%Y-%m') = '$currentMonth'");
    $count = $check->fetch_assoc()['count'];
    
    if ($count > 0) {
        return;
    }

    $recurring_incomes = $conn->query("
        SELECT idUser, idCard, amountIn, descriptionIn 
        FROM incomes 
        WHERE isRecurring = 1 
        AND DATE_FORMAT(dateIn, '%Y-%m') = '$lastMonth'
    ");
    
    while($income = $recurring_incomes->fetch_assoc()) {
        $stmt = $conn->prepare("
            INSERT INTO incomes (idUser, idCard, amountIn, dateIn, descriptionIn, isRecurring) 
            VALUES (?, ?, ?, CURDATE(), ?, 1)
        ");
        $stmt->bind_param("iids", 
            $income['idUser'],
            $income['idCard'],
            $income['amountIn'],
            $income['descriptionIn']
        );
        $stmt->execute();
 
        $conn->query("
            UPDATE cards 
            SET currentBalance = currentBalance + {$income['amountIn']} 
            WHERE idCard = {$income['idCard']}
        ");
    }

    $recurring_expenses = $conn->query("
        SELECT idUser, idCard, amountEx, descriptionEx, category 
        FROM expenses 
        WHERE isRecurring = 1 
        AND DATE_FORMAT(dateEx, '%Y-%m') = '$lastMonth'
    ");
    
    while($expense = $recurring_expenses->fetch_assoc()) {
        $stmt = $conn->prepare("
            INSERT INTO expenses (idUser, idCard, amountEx, dateEx, descriptionEx, category, isRecurring) 
            VALUES (?, ?, ?, CURDATE(), ?, ?, 1)
        ");
        $stmt->bind_param("iidss", 
            $expense['idUser'],
            $expense['idCard'],
            $expense['amountEx'],
            $expense['descriptionEx'],
            $expense['category']
        );
        $stmt->execute();

        $conn->query("
            UPDATE cards 
            SET currentBalance = currentBalance - {$expense['amountEx']} 
            WHERE idCard = {$expense['idCard']}
        ");
    }
}
?>