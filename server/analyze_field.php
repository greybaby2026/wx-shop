<?php
// 数据库配置 - 源数据库
$host = '127.0.0.1';
$database = 'jiangjunshijia_c';
$username = 'jiangjunshijia_c';
$password = 'r4JsLcrrtm';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== 分析源数据库 jiangjunshijia_c 中的 team_activity 相关表 ===\n\n";

    // 1. 检查是否有 team_activity 表
    echo "【1. 检查 team_activity 表是否存在】\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'team_activity'");
    if ($stmt->rowCount() > 0) {
        echo "team_activity 表存在\n\n";

        echo "【2. team_activity 表结构】\n";
        $stmt = $pdo->query("DESCRIBE team_activity");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Default']}\n";
        }
        echo "\n";

        echo "【3. team_activity 表数据示例】\n";
        $stmt = $pdo->query("SELECT * FROM team_activity LIMIT 3");
        $sampleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($sampleData) > 0) {
            foreach ($sampleData as $data) {
                print_r($data);
            }
        } else {
            echo "表为空\n";
        }
    } else {
        echo "team_activity 表不存在\n\n";
    }
    echo "\n";

    // 2. 查找所有包含 team_activity 相关字段的表
    echo "【4. 源数据库中所有包含 team_activity 相关字段的表】\n";
    $stmt = $pdo->query("
        SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, COLUMN_TYPE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '$database' AND COLUMN_NAME LIKE '%team_activity%'
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "表: {$row['TABLE_NAME']}, 字段: {$row['COLUMN_NAME']}, 类型: {$row['DATA_TYPE']}\n";
    }
    echo "\n";

    // 3. 检查订单表结构
    echo "【5. 检查 order 表（或类似订单表）的 team_info 字段】\n";
    $stmt = $pdo->query("SHOW TABLES LIKE '%order%'");
    $orderTables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($orderTables as $table) {
        $tableName = $table['Tables_in_jiangjunshijia_c (%order%)'];
        echo "\n表名: $tableName\n";

        // 检查是否有 team_info 或 team_activity_id 字段
        $stmt2 = $pdo->query("DESCRIBE $tableName");
        while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            if (strpos($row['Field'], 'team') !== false || strpos($row['Field'], 'activity') !== false) {
                echo "  {$row['Field']} - {$row['Type']}\n";
            }
        }
    }

} catch (PDOException $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
