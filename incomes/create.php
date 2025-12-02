<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smart_wallet";

$income = trim($_POST['income'] ?? '');
$dateIn = trim($_POST["dateIn"] ?? '');
$descriptionIn = trim($_POST["descriptionIn"] ?? '');

if($income !== '' && $dateIn !== '' && $descriptionIn !== ''){
    $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO incomes (amountIn,dateIn,descriptionIn) VALUES (?,?,?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("dss", $income,$dateIn,$descriptionIn);
        $stmt->execute();

        $stmt->close();
        $conn->close();
}

header("Location: list.php");
exit;
?>