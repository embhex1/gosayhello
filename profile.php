<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$username = 'embhex'; // Hardcoded username for now

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $validPassword = 'Makl4mp1r@$'; // Current password hardcoded

    if ($currentPassword !== $validPassword) {
        $error = 'Current password is incorrect.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New password and confirmation do not match.';
    } elseif (empty($newPassword)) {
        $error = 'New password cannot be empty.';
    } else {
        // Here you would update the password in your user store
        // Since this is a demo, just show success message
        $success = 'Password changed successfully (not really, demo only).';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-['Roboto'],sans-serif min-h-screen flex items-center justify-center p-6">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 text-center">Profile</h1>
        <p class="mb-6 text-center text-gray-700">Username: <strong><?= htmlspecialchars($username) ?></strong></p>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="profile.php" class="space-y-4">
            <div>
                <label for="current_password" class="block mb-1 font-semibold text-gray-700">Current Password</label>
                <input type="password" id="current_password" name="current_password" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="new_password" class="block mb-1 font-semibold text-gray-700">New Password</label>
                <input type="password" id="new_password" name="new_password" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label for="confirm_password" class="block mb-1 font-semibold text-gray-700">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded transition duration-300">Change Password</button>
        </form>
        <div class="mt-4 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline font-semibold">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
