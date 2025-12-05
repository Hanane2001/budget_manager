<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['idEx'] ?? '';
    $amount = $_POST['amountEx'] ?? '';
    $date = $_POST['dateEx'] ?? '';
    $description = $_POST['descriptionEx'] ?? '';

    if (!empty($id) && !empty($amount) && !empty($date)) {
        $id = intval($id);
        $amount = floatval($amount);
        $date = $conn->real_escape_string($date);
        $description = $conn->real_escape_string($description);
        
        $sql = "UPDATE expenses SET amountEx = ?, dateEx = ?, descriptionEx = ? WHERE idEx = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dssi", $amount, $date, $description, $id);
        
        if ($stmt->execute()) {
            header("Location: list.php?message=expense_updated");
        } else {
            header("Location: list.php?error=update_failed");
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