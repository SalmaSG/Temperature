<?php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "hum_temp_db";

$conn = mysqli_connect($host,$user,$password,$dbname);

if(!$conn)
{
die("Connection failed");
}

?>