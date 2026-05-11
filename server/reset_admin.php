<?php
// 数据库配置
$host = '127.0.0.1';
$database = 'jiangjunshijia_c';
$username = 'jiangjunshijia_c';
$password = 'r4JsLcrrtm';

echo "Connecting to database...\n";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 生成新密码: 123456
    // 加密方式: md5($salt . md5($plaintext . $salt))
    // $salt = Config::get('project.unique_identification') = '934c'
    $salt = '934c';
    $pwd = '123456';
    $encryptedPassword = md5($salt . md5($pwd . $salt));

    echo "Salt: $salt\n";
    echo "Password (123456): $encryptedPassword\n";

    // 更新 admin 表 (id=1 的超级管理员)
    $sql = "UPDATE ls_admin SET password = '$encryptedPassword' WHERE id = 1";
    $stmt = $pdo->exec($sql);

    echo "Admin password reset to: 123456\n";
    echo "Affected rows: $stmt\n";

    // 显示结果
    $stmt = $pdo->query("SELECT id, account, password FROM ls_admin WHERE id = 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($admin);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
