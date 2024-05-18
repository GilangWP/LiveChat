<?php
$sName = "localhost"; // server name
$uName = "root"; // user name
$pass = ""; // password
$db_name = "chat_app_db"; // database name

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>