<?php
session_start();

$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$adminUser = 'admin';
$adminPass = 'admin123';
$loginError = '';

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($username === $adminUser && $password === $adminPass) {
        $_SESSION['loggedin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $loginError = 'Invalid username or password.';
    }
}

if (isset($_POST['upload']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['file']['name']);
        $targetFile = $uploadDir . '/' . time() . '-' . $filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            header('Location: index.php');
            exit;
        } else {
            $uploadError = 'Failed to move uploaded file.';
        }
    } else {
        $uploadError = 'No file uploaded or upload error.';
    }
}

$files = array_diff(scandir($uploadDir), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>File Upload and Download</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-6">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">File Upload and Download</h1>

    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true): ?>
        <div class="w-full max-w-md bg-white p-6 rounded shadow mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Admin Login</h2>
            <?php if ($loginError): ?>
                <p class="text-red-600 mb-4"><?= htmlspecialchars($loginError) ?></p>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block mb-1 font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" required class="block w-full text-gray-700 border border-gray-300 rounded px-3 py-2" />
                </div>
                <div>
                    <label for="password" class="block mb-1 font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required class="block w-full text-gray-700 border border-gray-300 rounded px-3 py-2" />
                </div>
                <button type="submit" name="login" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded transition duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
            </form>
        </div>
    <?php else: ?>
        <form method="POST" enctype="multipart/form-data" class="mb-8 w-full max-w-md bg-white p-6 rounded shadow">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Upload File</h2>
                <a href="?logout=1" class="text-red-600 hover:underline flex items-center">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
            <?php if (!empty($uploadError)): ?>
                <p class="text-red-600 mb-4"><?= htmlspecialchars($uploadError) ?></p>
            <?php endif; ?>
            <label for="file" class="block mb-2 font-medium text-gray-700">Select a file to upload</label>
            <input type="file" id="file" name="file" required class="block w-full text-gray-700 border border-gray-300 rounded px-3 py-2 mb-4" />
            <button type="submit" name="upload" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition duration-200">
                <i class="fas fa-upload mr-2"></i> Upload File
            </button>
        </form>
    <?php endif; ?>

    <section class="w-full max-w-md bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Available Files for Download</h2>
        <ul class="space-y-2 text-gray-700">
            <?php if (empty($files)): ?>
                <li>No files uploaded yet.</li>
            <?php else: ?>
                <?php foreach ($files as $file): ?>
                    <li class="flex items-center justify-between border border-gray-200 rounded px-3 py-2 hover:bg-gray-50">
                        <span><?= htmlspecialchars($file) ?></span>
                        <a href="uploads/<?= rawurlencode($file) ?>" download class="text-blue-600 hover:underline flex items-center">
                            <i class="fas fa-download mr-1"></i> Download
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </section>
</body>
</html>
