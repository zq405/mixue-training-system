<?php
$conn=new mysqli("localhost","root","","mixue_db");

if($conn->connect_error)
    {
        die("Connection lost".$conn->connect_error);
    }
$name=$_POST['name'];
$sql="INSERT INTO employees(name) VALUES('$name')";

if($conn->query($sql)===TRUE)
    {
        echo"Successfull added <a href='index.php'>Return</a>";
    }
else
    {
        echo"Error".$conn->error;
    }
$conn->close();
?>