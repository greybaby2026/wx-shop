<?php
// 数据库配置 - 源数据库
$host = '127.0.0.1';
$database = 'jiangjunshijia_c';
$username = 'jiangjunshijia_c';
$password = 'r4JsLcrrtm';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== 分析源数据库中需要添加 team_activity_id 字段的表 ===\n\n";

    // 常见需要 team_activity_id 的表（基于商城系统经验）
    $likelyTables = [
        'ls_order',           // 订单表
        'ls_order_goods',     // 订单商品表
        'ls_team_found',      // 拼团记录表
        'ls_team_join',       // 参团记录表
    ];

    echo "【1. 检查常见表是否存在】\n";
    foreach ($likelyTables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "  ✓ $table 存在\n";
        } else {
            echo "  - $table 不存在\n";
        }
    }

    echo "\n【2. 检查 order 表结构】\n";
    $stmt = $pdo->query("DESCRIBE ls_order");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }

    echo "\n【3. 检查 order_goods 表结构】\n";
    $stmt = $pdo->query("DESCRIBE ls_order_goods");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }

    echo "\n【4. 查找所有包含 team 字段的表】\n";
    $stmt = $pdo->query("
        SELECT TABLE_NAME, COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '$database' AND COLUMN_NAME LIKE '%team%'
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "表: {$row['TABLE_NAME']}, 字段: {$row['COLUMN_NAME']}\n";
    }

} catch (PDOException $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
