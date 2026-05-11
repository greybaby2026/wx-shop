<?php
// 数据库配置
$host = '127.0.0.1';
$database = 'new_jiangjunshij';
$username = 'jiangjunshijia_c';
$password = 'r4JsLcrrtm';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== 查找目标数据库中所有包含 team_activity_id 字段的表 ===\n\n";

    // 查找所有包含 team_activity_id 字段的表及其详细信息
    $stmt = $pdo->query("
        SELECT
            TABLE_NAME,
            COLUMN_NAME,
            DATA_TYPE,
            COLUMN_TYPE,
            IS_NULLABLE,
            COLUMN_DEFAULT,
            COLUMN_COMMENT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '$database' AND COLUMN_NAME = 'team_activity_id'
        ORDER BY TABLE_NAME
    ");

    $tables = [];
    echo "【包含 team_activity_id 字段的表】\n";
    echo str_repeat("-", 80) . "\n";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "表名: {$row['TABLE_NAME']}\n";
        echo "  - 数据类型: {$row['DATA_TYPE']}\n";
        echo "  - 字段类型: {$row['COLUMN_TYPE']}\n";
        echo "  - 可空: {$row['IS_NULLABLE']}\n";
        echo "  - 默认值: " . ($row['COLUMN_DEFAULT'] ?? 'NULL') . "\n";
        echo "  - 注释: {$row['COLUMN_COMMENT']}\n\n";
        $tables[] = $row['TABLE_NAME'];
    }

    echo "共找到 " . count($tables) . " 个表包含 team_activity_id 字段\n\n";

    // 生成 ALTER TABLE 语句
    echo "【生成 ALTER TABLE 语句（适用于源数据库 jiangjunshijia_c）】\n";
    echo str_repeat("-", 80) . "\n";

    foreach ($tables as $table) {
        $stmt = $pdo->query("DESCRIBE $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['Field'] === 'team_activity_id') {
                $nullable = $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $row['Default'] !== null ? " DEFAULT '{$row['Default']}'" : ($row['Null'] === 'YES' ? ' DEFAULT NULL' : '');
                echo "ALTER TABLE `$table` ADD COLUMN `team_activity_id` {$row['Type']} $nullable$default COMMENT '{$row['Comment']}';\n";
            }
        }
    }

} catch (PDOException $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
