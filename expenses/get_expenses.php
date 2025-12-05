<?php
include '../config/database.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM expenses ORDER BY dateEx DESC";
$result = $conn->query($sql);

$expenses = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $expenses[] = $row;
    }
}

echo json_encode($expenses);
$conn->close();
?>