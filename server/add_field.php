<?php
// 数据库配置
$host = '127.0.0.1';
$database = 'jiangjunshijia_c';
$username = 'jiangjunshijia_c';
$password = 'r4JsLcrrtm';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== 在源数据库中添加 team_activity_id 字段 ===\n\n";

    // 检查字段是否已存在
    $stmt = $pdo->query("
        SELECT COUNT(*) as cnt
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '$database' AND TABLE_NAME = 'ls_order' AND COLUMN_NAME = 'team_activity_id'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['cnt'] > 0) {
        echo "[*] 字段 team_activity_id 已存在于 ls_order 表中，无需添加\n";
    } else {
        // 添加字段（设置为 NULL，不影响现有数据）
        $sql = "ALTER TABLE `ls_order` ADD COLUMN `team_activity_id` INT(10) UNSIGNED NULL COMMENT '拼团活动ID' AFTER `is_team_success`";
        $pdo->exec($sql);
        echo "[✓] 成功在 ls_order 表中添加 team_activity_id 字段\n";
        echo "    - 字段类型: INT(10) UNSIGNED\n";
        echo "    - 可为空: YES (NULL)\n";
        echo "    - 默认值: NULL\n";
        echo "    - 注释: 拼团活动ID\n";
    }

    echo "\n=== 验证字段添加结果 ===\n";
    $stmt = $pdo->query("DESCRIBE ls_order");
    $found = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Field'] === 'team_activity_id') {
            echo "[✓] 字段验证成功！\n";
            echo "    字段名: {$row['Field']}\n";
            echo "    类型: {$row['Type']}\n";
            echo "    可空: {$row['Null']}\n";
            echo "    默认值: {$row['Default']}\n";
            $found = true;
            break;
        }
    }

    if (!$found) {
        echo "[!] 字段添加验证失败\n";
    }

    echo "\n[✓] 操作完成！现有数据不受影响。\n";

} catch (PDOException $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
