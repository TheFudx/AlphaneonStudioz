<?php

require __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();


if(env('APP_ENV') == 'local'){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "alphastudioz_ott";
}else{
    $servername = "69.16.233.70";
    $username = "alphastudioz_ott";
    $password = "Pass@66466";
    $database = "alphastudioz_ott";
}

// $servername = "69.16.233.70";
// $username = "alphastudioz_copy";
// $password = "w)%LNxy#05+X";
// $database = "alphastudioz_copy";

$connectNow = mysqli_connect($servername, $username, $password, $database);

if ($connectNow->connect_error) {
    die("Connection failed: " . $connectNow->connect_error);
}
?>
