<?php
// 复制编译文件脚本
$source = '/tmp/web-build/dist';
$target = '/www/wwwroot/jiangjunshijia_com/web/dist';

function copyDirectory($source, $target) {
    if (!is_dir($target)) {
        if (!mkdir($target, 0755, true)) {
            echo "无法创建目录: $target\n";
            return false;
        }
    }
    
    $dir = opendir($source);
    if (!$dir) {
        echo "无法打开源目录: $source\n";
        return false;
    }
    
    while (false !== ($file = readdir($dir))) {
        if ($file != '.' && $file != '..') {
            $sourcePath = $source . '/' . $file;
            $targetPath = $target . '/' . $file;
            
            if (is_dir($sourcePath)) {
                copyDirectory($sourcePath, $targetPath);
            } else {
                if (!copy($sourcePath, $targetPath)) {
                    echo "复制失败: $sourcePath -> $targetPath\n";
                }
            }
        }
    }
    
    closedir($dir);
    return true;
}

echo "开始复制编译文件...\n";
echo "源目录: $source\n";
echo "目标目录: $target\n";

if (copyDirectory($source, $target)) {
    echo "复制完成！\n";
    
    // 统计文件数量
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($target)
    );
    $fileCount = 0;
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $fileCount++;
        }
    }
    echo "共复制了 $fileCount 个文件\n";
} else {
    echo "复制失败！\n";
}
?>
