<?php
$filesDir = __DIR__ . '/files';

// Handle file download request
if (isset($_GET['file'])) {
    $requestedFile = $_GET['file'];
    $filePath = realpath($filesDir . '/' . $requestedFile);

    // Security check: ensure the file is inside the files directory
    if ($filePath && strpos($filePath, realpath($filesDir)) === 0 && is_file($filePath)) {
        $fileName = basename($filePath);
        // Set headers to force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        flush();
        readfile($filePath);
        exit;
    } else {
        $error = "File not found or invalid.";
    }
}

// Recursive function to scan files directory and build nested array of categories, subcategories, and files
function scanFilesDir($dir, $baseDir) {
    $result = [];
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $subDirFiles = scanFilesDir($path, $baseDir);
            // Ensure subDirFiles is always an array
            if (!is_array($subDirFiles)) {
                $subDirFiles = [];
            }
            $result[$item] = $subDirFiles;
        } else {
            // Store relative path from baseDir for download link
            $relativePath = substr($path, strlen($baseDir) + 1);
            $result[] = $relativePath;
        }
    }
    return $result;
}

$filesTree = scanFilesDir($filesDir, $filesDir);

// Remove category "0" if exists
if (isset($filesTree['0'])) {
    unset($filesTree['0']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Download Files</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-['Roboto'],sans-serif min-h-screen flex flex-col items-center p-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-800"> <i class="fas fa-download mr-2"></i>Download Files</h1>
    <div class="fixed top-4 right-4 flex space-x-2 z-50">
        <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-3 rounded text-sm transition duration-300 inline-block text-center">Login</a>
    </div>

    <input type="text" id="searchInput" placeholder="Search files..." aria-label="Search files" class="mb-6 w-full max-w-4xl p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />

    <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded mb-6 w-full max-w-4xl text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="w-full max-w-4xl bg-white rounded shadow p-6">
        <?php if (empty($filesTree)): ?>
            <p class="text-gray-600 text-center">No files available for download.</p>
        <?php else: ?>
            <?php
            // Recursive function to render categories, subcategories, and files
            function renderFilesTree($tree) {
                if (!is_array($tree)) {
                    // Defensive check: if $tree is not an array, do nothing
                    return;
                }
                echo '<ul class="list-disc list-inside space-y-2">';
                foreach ($tree as $key => $value) {
                    if (is_array($value)) {
                        // Category or subcategory
                        echo '<li>';
                        echo '<span class="font-semibold text-lg text-gray-800">' . htmlspecialchars($key) . '</span>';
                        renderFilesTree($value);
                        echo '</li>';
                    } else {
                        // File
                        echo '<li class="flex justify-between items-center border-b border-gray-200 py-1">';
                        echo '<span class="text-gray-700"><i class="fas fa-file mr-2 text-gray-500"></i>' . htmlspecialchars(basename($value)) . '</span>';
                        echo '<a href="?file=' . urlencode($value) . '" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center">';
                        echo '<i class="fas fa-download mr-1"></i>Download</a>';
                        echo '</li>';
                    }
                }
                echo '</ul>';
            }

            // Render each top-level category separately with visual separation and dropdown toggle
            foreach ($filesTree as $category => $content) {
                $categoryId = 'category-' . md5($category);
                echo '<section class="mb-8 p-6 bg-white rounded shadow">';
                echo '<button aria-expanded="false" aria-controls="' . $categoryId . '" class="w-full flex justify-between items-center text-2xl font-bold mb-4 text-gray-900 focus:outline-none" onclick="toggleDropdown(\'' . $categoryId . '\', this)">';
                echo htmlspecialchars($category);
                echo '<i class="fas fa-chevron-down transition-transform duration-300" style="transform: rotate(-90deg);"></i>';
                echo '</button>';
                echo '<div id="' . $categoryId . '" class="overflow-hidden transition-max-height duration-300 max-h-0">';
                renderFilesTree($content);
                echo '</div>';
                echo '</section>';
            }
            ?>
        <?php endif; ?>
    </div>

    <script>
        function toggleDropdown(id, button) {
            const content = document.getElementById(id);
            const expanded = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', !expanded);
            if (expanded) {
                content.style.maxHeight = '0';
                button.querySelector('i').style.transform = 'rotate(-90deg)';
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                button.querySelector('i').style.transform = 'rotate(0deg)';
            }
        }

        // Initialize all dropdowns as collapsed
        document.querySelectorAll('button[aria-expanded]').forEach(button => {
            const id = button.getAttribute('aria-controls');
            const content = document.getElementById(id);
            content.style.maxHeight = '0';
            button.querySelector('i').style.transform = 'rotate(-90deg)';
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const sections = document.querySelectorAll('section.mb-8');
            sections.forEach(section => {
                const button = section.querySelector('button');
                const content = section.querySelector('div');
                let hasMatch = false;

                // Check if category name matches
                if (button.textContent.toLowerCase().includes(filter)) {
                    hasMatch = true;
                }

                // Check files and subcategories inside content
                const items = content.querySelectorAll('li');
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(filter)) {
                        item.style.display = '';
                        hasMatch = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Show or hide the entire section based on match
                if (hasMatch) {
                    section.style.display = '';
                    // Expand section if filter is not empty
                    if (filter) {
                        content.style.maxHeight = content.scrollHeight + 'px';
                        button.setAttribute('aria-expanded', 'true');
                        button.querySelector('i').style.transform = 'rotate(0deg)';
                    } else {
                        content.style.maxHeight = '0';
                        button.setAttribute('aria-expanded', 'false');
                        button.querySelector('i').style.transform = 'rotate(-90deg)';
                    }
                } else {
                    section.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
