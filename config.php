<?php
$host = "localhost";
$dbname = "reservation_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
define("SMTP_HOST", "smtp.gmail.com"); 
define("SMTP_USER", "yanngaouditz@gmail.com");
define("SMTP_PASS", "kvjp lswg qzan fqca");
define("SMTP_PORT", 587); 
define("SMTP_FROM", "yanngaouditz@gmail.com");

?>
