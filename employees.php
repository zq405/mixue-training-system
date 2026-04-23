<?php
session_start();
include 'config.php';

if(!isset(($_SESSION['user'])))
    {
        header("Location: login.php");
    }

$result=$conn->query("SELECT * FROM employees");

while($row=$result->fetch_assoc())
    {
        echo"<p>".$row['name']."</p>";
    }
?>