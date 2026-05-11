<?php
// OPcache 状态检查脚本
header('Content-Type: text/html; charset=utf-8');
$opcache_loaded = extension_loaded('opcache');
$status = $opcache_loaded ? '<span style="color:green;font-weight:bold">✅ 已加载</span>' : '<span style="color:red;font-weight:bold">❌ 未加载</span>';
echo "<h2>PHP OPcache 状态检测</h2>";
echo "<p>OPcache 加载状态: {$status}</p>";
echo "<p>PHP SAPI: " . php_sapi_name() . "</p>";
echo "<p>PHP 版本: " . PHP_VERSION . "</p>";
echo "<p>ini 文件: " . php_ini_loaded_file() . "</p>";
echo "<hr><h3>OPcache 配置参数</h3><table border='1' cellpadding='5'>";
echo "<tr><th>参数</th><th>值</th></tr>";
$params = ['opcache.enable','opcache.memory_consumption','opcache.interned_strings_buffer','opcache.max_accelerated_files','opcache.revalidate_freq','opcache.fast_shutdown','opcache.save_comments','opcache.enable_file_override','opcache.enable_cli','opcache.validate_timestamps'];
foreach($params as $p){
    $v = ini_get($p);
    echo "<tr><td>{$p}</td><td>".($v!==false?$v:'NOT SET')."</td></tr>";
}
echo "</table>";
if($opcache_loaded){
    echo "<hr><h3>OPcache 运行统计</h3><pre>";
    print_r(opcache_get_status(true));
    echo "</pre>";
}
