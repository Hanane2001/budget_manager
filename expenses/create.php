<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smart_wallet";

$expense = trim($_POST['expense'] ?? '');
$dateEx = trim($_POST["dateEx"] ?? '');
$descriptionEx = trim($_POST["descriptionEx"] ?? '');

if($expense !== '' && $dateEx !== '' && $descriptionEx !== ''){
    $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO expenses (amountEx,dateEx,descriptionEx) VALUES (?,?,?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("dss", $expense,$dateEx,$descriptionEx);
        $stmt->execute();

        $stmt->close();
        $conn->close();
}


header("Location: list.php");
exit;
?>