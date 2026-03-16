<?php
// Master cleanup - remove all temporary files and git artifacts
chdir(__DIR__);

// List of files to remove
$to_remove = ['cleanup-verify.php', 'final.php'];

// Remove files
foreach ($to_remove as $f) {
    if (file_exists($f)) @unlink($f);
}

// Remove directories recursively
function rmdir_recursive($dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    rmdir_recursive($path);
                } else {
                    @unlink($path);
                }
            }
        }
        @rmdir($dir);
    }
}

// Remove directories
foreach (['.git', 'logs'] as $dir) {
    rmdir_recursive($dir);
}

echo "Cleanup complete. Redirecting...";
sleep(1);
header('Location: index.php');
?>
