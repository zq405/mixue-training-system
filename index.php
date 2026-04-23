<?php
// index.php - 修复版本
session_start();

// 调试模式（临时开启，部署后关闭）
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// 检查用户是否已登录
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

// 如果未登录，跳转到登录页
header("Location: login.php");
exit();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Mixue Training System</title>
    <style>
        body{font-family: Arial;margin:0;background: #fff5f5;}
        header{background: #d6001c;color: white;padding: 20px; text-align: center;}
        nav{background: #ffccd5;padding: 10px;text-align: center;}
        nav a{margin: 10px; text-decoration: none;color: #d6001c;font-weight: bold;}
        section{padding: 20px;}
        .card{background: white;padding: 15px;margin: 15px 0;border-radius: 10px;box-shadow: 0 2px 5px rgba(0,0,0,0.1);}
        footer{background: #d6001c;color: white;text-align: center;padding: 10px;}
    </style>
</head>
<body>
    <header>
        <h1>Mixue Training System</h1>
    </header>
    <section>
        <h2>Staff List</h2>
        <?php
        $conn=new mysqli("localhost","root","","mixue_db");
        if($conn->connect_error)
            {
                die("unable to connect".$conn->connect_error);
            }
        $sql="SELECT id,name";
        $result=$conn->query($sql);
        if($result->num_rows>0)
            {
                while($row=$result->fetch_assoc())
                    {
                        echo"<div class='card'>";
                        echo"<p>ID:".$row["id"]."</p>";
                        echo"<p>Name:".$row["name"]."</p>";
                        echo"</div>";
                    }
            }
        else
            {
                echo"Dont have staff data";
            }
        $conn->close();
        ?>
    </section>
    <section>
        <h2>Add new staff</h2>
        <form method="POST" action="add.php">
            <input type="text" name="name" placeholder="Name" required><br><br>
            <button type="submit">Add</button>
        </form>
    </section>

    <footer>
        <p>@ 2026 training system</p>
    </footer>
</body>
</html>