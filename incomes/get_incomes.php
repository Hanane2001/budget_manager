<?php
include '../config/database.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM incomes ORDER BY dateIn DESC";
$result = $conn->query($sql);

$incomes = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $incomes[] = $row;
    }
}

echo json_encode($incomes);
$conn->close();
?>