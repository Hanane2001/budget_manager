<?php
include '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    $errors = [];
    
    if (empty($fullName)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }

    $checkEmail = $conn->prepare("SELECT idUser FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();
    
    if ($checkEmail->num_rows > 0) {
        $errors[] = "Email already registered";
    }
    $checkEmail->close();

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (fullName, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullName, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            $userId = $stmt->insert_id;

            $defaultCardStmt = $conn->prepare("INSERT INTO cards (idUser, cardName, bankName, isMain, currentBalance) VALUES (?, ?, ?, ?, ?)");
            $defaultCardName = "Main Card";
            $defaultBank = "Default Bank";
            $isMain = 1;
            $initialBalance = 0.00;
            $defaultCardStmt->bind_param("issid", $userId, $defaultCardName, $defaultBank, $isMain, $initialBalance);
            $defaultCardStmt->execute();
            $defaultCardStmt->close();
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $fullName;
            
            header("Location: ../dashboard.php");
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
        $stmt->close();
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: register.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex justify-center">
                    <i class="fas fa-wallet text-blue-600 text-5xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
                <p class="mt-2 text-center text-sm text-gray-600">Or <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500">sign in to existing account</a></p>
            </div>
            
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <form class="mt-8 space-y-6" action="register.php" method="POST">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div class="mb-4">
                        <label for="fullName" class="block text-gray-700 mb-2">Full Name</label>
                        <input id="fullName" name="fullName" type="text" required class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                        <input id="email" name="email" type="email" required class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 mb-2">Password</label>
                        <input id="password" name="password" type="password" required class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="confirmPassword" class="block text-gray-700 mb-2">Confirm Password</label>
                        <input id="confirmPassword" name="confirmPassword" type="password" required class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Register</button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="text-blue-600 hover:text-blue-500"><i class="fas fa-arrow-left mr-2"></i>Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>