<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$uploadDir = __DIR__ . '/files';
$maxFileSize = 10 * 1024 * 1024 * 1024; // 10 GB in bytes
$uploadError = '';
$uploadSuccess = '';

// Scan categories and subcategories
function scanCategories($dir) {
    $result = [];
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $subDirs = scanCategories($path);
            $result[$item] = $subDirs;
        }
    }
    return $result;
}

$categories = scanCategories($uploadDir);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    $file = $_FILES['fileToUpload'];
    $category = $_POST['category'] ?? '';
    $subcategory = $_POST['subcategory'] ?? '';

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadError = 'File upload error. Please try again.';
    } elseif ($file['size'] > $maxFileSize) {
        $uploadError = 'File size exceeds the maximum allowed size of 10 GB.';
    } else {
        $targetDir = $uploadDir;
        if ($category && isset($categories[$category])) {
            $targetDir .= '/' . $category;
            if ($subcategory && isset($categories[$category][$subcategory])) {
                $targetDir .= '/' . $subcategory;
            }
        }
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . '/' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $uploadSuccess = 'File uploaded successfully.';
        } else {
            $uploadError = 'Failed to move uploaded file.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload File</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-['Roboto'],sans-serif min-h-screen flex items-center justify-center p-6">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 text-center">Upload File</h1>

        <?php if ($uploadError): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center"><?= htmlspecialchars($uploadError) ?></div>
        <?php elseif ($uploadSuccess): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center"><?= htmlspecialchars($uploadSuccess) ?></div>
        <?php endif; ?>

        <form action="upload.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= $maxFileSize ?>" />
            <div>
                <label for="category" class="block mb-1 font-semibold text-gray-700">Category</label>
                <select id="category" name="category" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat => $subcats): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="subcategory" class="block mb-1 font-semibold text-gray-700">Subcategory</label>
                <select id="subcategory" name="subcategory" class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                    <option value="">-- Select Subcategory --</option>
                </select>
            </div>
            <div>
                <label for="fileToUpload" class="block mb-1 font-semibold text-gray-700">Select file to upload (max 10 GB):</label>
                <input type="file" name="fileToUpload" id="fileToUpload" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded transition duration-300">Upload</button>
        </form>

        <div class="mt-4 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline font-semibold">Back to Dashboard</a>
        </div>
    </div>

    <script>
        const categories = <?= json_encode($categories) ?>;
        const categorySelect = document.getElementById('category');
        const subcategorySelect = document.getElementById('subcategory');

        categorySelect.addEventListener('change', () => {
            const selectedCategory = categorySelect.value;
            subcategorySelect.innerHTML = '<option value="">-- Select Subcategory --</option>';
            if (selectedCategory && categories[selectedCategory]) {
                const subcats = Object.keys(categories[selectedCategory]);
                if (subcats.length > 0) {
                    subcategorySelect.disabled = false;
                    subcats.forEach(subcat => {
                        const option = document.createElement('option');
                        option.value = subcat;
                        option.textContent = subcat;
                        subcategorySelect.appendChild(option);
                    });
                } else {
                    subcategorySelect.disabled = true;
                }
            } else {
                subcategorySelect.disabled = true;
            }
        });
    </script>
</body>
</html>
