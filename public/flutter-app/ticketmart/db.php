<?php


$servername = "localhost";
$username = "u862132972_tmartdev";
$password = ";Y5;Mpc:vz";
$database = "u862132972_tmart";

$connectNow = mysqli_connect($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
