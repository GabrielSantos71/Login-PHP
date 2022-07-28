<?php

    session_start();

    $sqluser = "user";
    $sqlpassword = "password";

    $sqldatabase = "login";

    $post = $_SERVER['REQUEST_METHOD']=='POST';
    if ($post) {
        if(
            empty($_POST['uname'])||
            empty($_POST['pass'])
        ) $empty_fields = true;

        else {
                try {
                    $pdo = new PDO("mysql:host=localhost;dbname=".$sqldatabase,$sqluser,$sqlpassword);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    exit($e->getMessage());
                }
                $st = $pdo->prepare('SELECT * FROM list WHERE user_name=?');
                $st->execute(array($_POST['uname']));
                $r=$st->fetch();
                if($r != null && $r["password"]==$_POST['pass']) {
                    echo $_POST["uname"];
                    echo $_POST["pass"];
                    $_SESSION["uname"] = $_POST["uname"];
                    $_SESSION["pass"] = $_POST["pass"];
                    $_SESSION["fname"] = $r["first_name"];
                    echo $_SESSION["uname"];
                    echo $_SESSION["pass"];
                    header("Location:success.php");
                    exit;
                } else $login_err = true;
        }
    }
?>

<!DOCTYPE HTML>
<html>
<head>
<style type="text/css">
</style>
</head> 
<body>
<div>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <p>Login</p>
    <?php 
    echo 'Username<br><input type="text" name="uname" value="" placeholder="Username"><br>';
    echo '<br>Password<br><input type="password" name="pass" value="" placeholder="Password"><br>';
    if(!empty($login_err)&&$login_err) echo "<span>Incorrect Username or password.</span>";
    if(!empty($empty_fields)&&$empty_fields) echo "<span>Enter username and password.</span>";
    ?>
    <br>
    <input type="submit" id="submit" value="Login"><br><br>
    Don't have a account? <a href="signup.php">SignUp</a>.<br><br>
</form>
</div>
</body>
</html>