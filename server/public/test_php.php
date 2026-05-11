<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing PHP execution...\n";
echo "PHP Version: " . PHP_VERSION . "\n";

try {
    require __DIR__ . '/../server/vendor/autoload.php';
    echo "Autoload OK\n";

    $app = new think\App();
    echo "App created OK\n";

    $request = think\facade\Request::instance();
    echo "Request instance OK\n";

    $version = $request->header('version');
    echo "Version header: " . ($version ?: 'EMPTY') . "\n";

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
