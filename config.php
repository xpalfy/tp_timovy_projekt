<?php

$servername = "localhost";
$dbusername = "root";
$dbpassword = "NemTudokJobbJelszavat123.";
$dbname = "TP_timovy_projekt";

function getDatabaseConnection()
{
    global $servername, $dbusername, $dbpassword, $dbname;
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("Connection error: " . $conn->connect_error);
    }
    return $conn;
}