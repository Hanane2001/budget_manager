<?php
include '../config/database.php';
session_start();

if (!isset($_SESSION['temp_user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    
    if (!empty($otp)) {
        $stmt = $conn->prepare("SELECT idUser, fullName, email, otp_expiry FROM users WHERE idUser = ? AND otp_code = ?");
        $stmt->bind_param("is", $_SESSION['temp_user_id'], $otp);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (strtotime($user['otp_expiry']) > time()) {
                $_SESSION['user_id'] = $user['idUser'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['fullName'];

                $clearOtp = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expiry = NULL WHERE idUser = ?");
                $clearOtp->bind_param("i", $user['idUser']);
                $clearOtp->execute();
                $clearOtp->close();

                unset($_SESSION['temp_user_id'], $_SESSION['temp_user_email'], $_SESSION['debug_otp']);
                
                header("Location: ../dashboard.php");
                exit();
            } else {
                $error = "OTP has expired";
            }
        } else {
            $error = "Invalid OTP code";
        }
        $stmt->close();
    } else {
        $error = "Please enter OTP code";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - SmartBudget</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex justify-center">
                    <i class="fas fa-shield-alt text-blue-600 text-5xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Two-Factor Authentication</h2>
                <p class="mt-2 text-center text-sm text-gray-600">Enter the 6-digit code sent to your email</p>
                <?php if (isset($_SESSION['debug_otp'])): ?>
                    <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        <p class="text-center font-bold">DEBUG: Your OTP is <?php echo $_SESSION['debug_otp']; ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <form class="mt-8 space-y-6" action="verify_otp.php" method="POST">
                <div>
                    <label for="otp" class="block text-gray-700 mb-2">6-digit OTP Code</label>
                    <input id="otp" name="otp" type="text" maxlength="6" required class="appearance-none rounded-lg relative block w-full px-3 py-3 text-center text-2xl font-bold border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Verify OTP</button>
                </div>
                
                <div class="text-center">
                    <a href="login.php" class="text-blue-600 hover:text-blue-500"><i class="fas fa-arrow-left mr-2"></i>Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>