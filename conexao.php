<?php

$dsn = "mysql:dbname=fb_trocas;host=127.0.0.1";
$dbuser = "root";
$dbpass = "";

    try {
        $db = new PDO($dsn, $dbuser, $dbpass);
    } catch (PDOException $e) {
        echo "Falhou: " . $e->getMessage();
    }


?>