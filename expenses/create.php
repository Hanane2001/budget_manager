<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amountEx'] ?? '';
    $date = $_POST['dateEx'] ?? '';
    $description = $_POST['descriptionEx'] ?? '';
    
    if (!empty($amount) && !empty($date)) {
        $amount = floatval($amount);
        $date = $conn->real_escape_string($date);
        $description = $conn->real_escape_string($description);

        $sql = "INSERT INTO expenses (amountEx, dateEx, descriptionEx) VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dss", $amount, $date, $description);
        
        if ($stmt->execute()) {
            header("Location: list.php?message=expense_added");
        } else {
            header("Location: list.php?error=insert_failed");
        }
        $stmt->close();
    } else {
        header("Location: list.php?error=missing_fields");
    }
} else {
    header("Location: list.php");
}

$conn->close();
exit();
?>