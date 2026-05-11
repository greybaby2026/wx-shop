<?php
// 数据库配置
$sourceConfig = [
    'host' => '127.0.0.1',
    'dbname' => 'jiangjunshijia_c',
    'username' => 'jiangjunshijia_c',
    'password' => 'r4JsLcrrtm'
];

$targetConfig = [
    'host' => '127.0.0.1',
    'dbname' => 'new_jiangjunshij',
    'username' => 'jiangjunshijia_c',
    'password' => 'r4JsLcrrtm'
];

echo "===========================================\n";
echo "       数据库迁移工具\n";
echo "从 jiangjunshijia_c 迁移到 new_jiangjunshij\n";
echo "===========================================\n\n";

try {
    // 连接源数据库
    $sourcePdo = new PDO(
        "mysql:host={$sourceConfig['host']};dbname={$sourceConfig['dbname']}",
        $sourceConfig['username'],
        $sourceConfig['password']
    );
    $sourcePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "[✓] 源数据库连接成功: {$sourceConfig['dbname']}\n";

    // 连接目标数据库
    $targetPdo = new PDO(
        "mysql:host={$targetConfig['host']};dbname={$targetConfig['dbname']}",
        $targetConfig['username'],
        $targetConfig['password']
    );
    $targetPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "[✓] 目标数据库连接成功: {$targetConfig['dbname']}\n\n";

    // 获取源数据库的所有表
    $sourceTables = [];
    $stmt = $sourcePdo->query("SHOW TABLES");
    while ($row = $stmt->fetch()) {
        $sourceTables[] = $row[0];
    }
    echo "[*] 源数据库表数量: " . count($sourceTables) . "\n";

    // 获取目标数据库的所有表
    $targetTables = [];
    $stmt = $targetPdo->query("SHOW TABLES");
    while ($row = $stmt->fetch()) {
        $targetTables[] = $row[0];
    }
    echo "[*] 目标数据库表数量: " . count($targetTables) . "\n\n";

    // 比较表结构并迁移数据
    $successCount = 0;
    $skipCount = 0;
    $errorCount = 0;
    $skippedTables = [];

    foreach ($sourceTables as $table) {
        if (!in_array($table, $targetTables)) {
            echo "[-] 表 [$table] 在目标数据库中不存在，跳过\n";
            $skipCount++;
            $skippedTables[] = $table;
            continue;
        }

        echo "\n[*] 处理表: $table\n";

        // 获取源表结构
        $sourceColumns = [];
        $stmt = $sourcePdo->query("DESCRIBE $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sourceColumns[$row['Field']] = $row;
        }

        // 获取目标表结构
        $targetColumns = [];
        $stmt = $targetPdo->query("DESCRIBE $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $targetColumns[$row['Field']] = $row;
        }

        // 找出共同的字段
        $commonColumns = array_intersect(array_keys($sourceColumns), array_keys($targetColumns));

        if (count($commonColumns) > 0) {
            $columns = implode(', ', $commonColumns);
            $placeholders = ':' . implode(', :', $commonColumns);

            // 清空目标表
            $targetPdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            $targetPdo->exec("TRUNCATE TABLE $table");

            // 从源表读取数据
            $stmt = $sourcePdo->query("SELECT $columns FROM $table");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($rows) > 0) {
                // 准备插入语句
                $insertSql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
                $insertStmt = $targetPdo->prepare($insertSql);

                // 批量插入数据
                $count = 0;
                foreach ($rows as $row) {
                    try {
                        $insertStmt->execute($row);
                        $count++;
                    } catch (PDOException $e) {
                        // 忽略插入错误，继续
                    }
                }

                echo "  [✓] 迁移了 $count 条记录 (" . count($commonColumns) . " 个字段)\n";
                $successCount++;
            } else {
                echo "  [-] 表为空，跳过\n";
                $skipCount++;
            }
            $targetPdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        } else {
            echo "  [!] 表结构完全不匹配，跳过\n";
            $skipCount++;
            $skippedTables[] = $table . " (结构不匹配)";
        }
    }

    echo "\n===========================================\n";
    echo "            迁移完成\n";
    echo "===========================================\n";
    echo "[✓] 成功迁移: $successCount 个表\n";
    echo "[-] 跳过: $skipCount 个表\n";
    echo "[!] 错误: $errorCount 个表\n\n";

    if (count($skippedTables) > 0) {
        echo "跳过的表:\n";
        foreach (array_unique($skippedTables) as $t) {
            echo "  - $t\n";
        }
    }

} catch (PDOException $e) {
    echo "\n[x] 错误: " . $e->getMessage() . "\n";
}
