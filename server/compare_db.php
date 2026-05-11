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

echo "================================================\n";
echo "       数据库表结构对比分析\n";
echo "源数据库: {$sourceConfig['dbname']}\n";
echo "目标数据库: {$targetConfig['dbname']}\n";
echo "================================================\n\n";

try {
    // 连接源数据库
    $sourcePdo = new PDO(
        "mysql:host={$sourceConfig['host']};dbname={$sourceConfig['dbname']}",
        $sourceConfig['username'],
        $sourceConfig['password']
    );
    $sourcePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "[✓] 源数据库连接成功\n";

    // 连接目标数据库
    $targetPdo = new PDO(
        "mysql:host={$targetConfig['host']};dbname={$targetConfig['dbname']}",
        $targetConfig['username'],
        $targetConfig['password']
    );
    $targetPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "[✓] 目标数据库连接成功\n\n";

    // 获取源数据库所有表
    $sourceTables = [];
    $stmt = $sourcePdo->query("SHOW TABLES");
    while ($row = $stmt->fetch()) {
        $sourceTables[] = $row[0];
    }

    // 获取目标数据库所有表
    $targetTables = [];
    $stmt = $targetPdo->query("SHOW TABLES");
    while ($row = $stmt->fetch()) {
        $targetTables[] = $row[0];
    }

    // ========== 1. 表级别对比 ==========
    echo "================================================\n";
    echo "【1. 表对比】\n";
    echo "================================================\n\n";

    $sourceOnlyTables = array_diff($sourceTables, $targetTables);
    $targetOnlyTables = array_diff($targetTables, $sourceTables);
    $commonTables = array_intersect($sourceTables, $targetTables);

    echo "源数据库独有表 (共 " . count($sourceOnlyTables) . " 个):\n";
    if (count($sourceOnlyTables) > 0) {
        foreach ($sourceOnlyTables as $table) {
            echo "  - $table\n";
        }
    } else {
        echo "  (无)\n";
    }
    echo "\n";

    echo "目标数据库独有表 (共 " . count($targetOnlyTables) . " 个):\n";
    if (count($targetOnlyTables) > 0) {
        foreach ($targetOnlyTables as $table) {
            echo "  + $table\n";
        }
    } else {
        echo "  (无)\n";
    }
    echo "\n";

    echo "共同表数量: " . count($commonTables) . " 个\n\n";

    // ========== 2. 字段级别对比 ==========
    echo "================================================\n";
    echo "【2. 字段对比（共同表）】\n";
    echo "================================================\n\n";

    $allDiffs = [];

    foreach ($commonTables as $table) {
        // 获取源表字段
        $sourceFields = [];
        $stmt = $sourcePdo->query("DESCRIBE $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sourceFields[$row['Field']] = $row;
        }

        // 获取目标表字段
        $targetFields = [];
        $stmt = $targetPdo->query("DESCRIBE $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $targetFields[$row['Field']] = $row;
        }

        $sourceOnlyFields = array_diff_key($sourceFields, $targetFields);
        $targetOnlyFields = array_diff_key($targetFields, $sourceFields);

        if (count($sourceOnlyFields) > 0 || count($targetOnlyFields) > 0) {
            $allDiffs[$table] = [
                'source_only' => $sourceOnlyFields,
                'target_only' => $targetOnlyFields
            ];
        }
    }

    if (count($allDiffs) > 0) {
        foreach ($allDiffs as $table => $diffs) {
            echo "表: $table\n";

            if (count($diffs['source_only']) > 0) {
                echo "  源数据库独有字段 (" . count($diffs['source_only']) . " 个):\n";
                foreach ($diffs['source_only'] as $field => $info) {
                    echo "    - $field ({$info['Type']})\n";
                }
            }

            if (count($diffs['target_only']) > 0) {
                echo "  目标数据库独有字段 (" . count($diffs['target_only']) . " 个):\n";
                foreach ($diffs['target_only'] as $field => $info) {
                    echo "    + $field ({$info['Type']})\n";
                }
            }
            echo "\n";
        }
    } else {
        echo "所有共同表的字段结构完全一致！\n\n";
    }

    // ========== 3. 统计汇总 ==========
    echo "================================================\n";
    echo "【3. 汇总统计】\n";
    echo "================================================\n\n";

    $sourceOnlyCount = 0;
    $targetOnlyCount = 0;

    foreach ($allDiffs as $table => $diffs) {
        $sourceOnlyCount += count($diffs['source_only']);
        $targetOnlyCount += count($diffs['target_only']);
    }

    echo "源数据库表总数: " . count($sourceTables) . "\n";
    echo "目标数据库表总数: " . count($targetTables) . "\n";
    echo "源数据库独有表: " . count($sourceOnlyTables) . " 个\n";
    echo "目标数据库独有表: " . count($targetOnlyTables) . " 个\n";
    echo "共同表: " . count($commonTables) . " 个\n";
    echo "有字段差异的表: " . count($allDiffs) . " 个\n";
    echo "源数据库独有字段: $sourceOnlyCount 个\n";
    echo "目标数据库独有字段: $targetOnlyCount 个\n\n";

    echo "================================================\n";
    echo "【4. 迁移建议】\n";
    echo "================================================\n\n";

    if (count($sourceOnlyTables) > 0) {
        echo "[*] 源数据库独有的表将不会被迁移:\n";
        foreach ($sourceOnlyTables as $table) {
            echo "    - $table\n";
        }
        echo "\n";
    }

    if ($targetOnlyCount > 0) {
        echo "[*] 目标数据库独有的字段将保留为空(NULL):\n";
        foreach ($allDiffs as $table => $diffs) {
            if (count($diffs['target_only']) > 0) {
                foreach ($diffs['target_only'] as $field => $info) {
                    echo "    - {$table}.{$field}\n";
                }
            }
        }
        echo "\n";
    }

    echo "[*] 迁移将使用共同字段进行数据同步\n";

} catch (PDOException $e) {
    echo "\n[x] 错误: " . $e->getMessage() . "\n";
}
