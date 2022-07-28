<?php

    session_start();

    $sqluser = "user";
    $sqlpassword = "password";

    $sqldatabase = "login";

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=".$sqldatabase,$sqluser,$sqlpassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        exit($e->getMessage());
    }
    $st = $pdo->prepare('SELECT * FROM list WHERE user_name=?');
    $st->execute(array($_SESSION["uname"]));
    if(($r=$st->fetch())==null||($r["password"]!=$_SESSION["pass"])) {
        header("Location:login.php");
        exit;
    }
    if ($_SERVER['REQUEST_METHOD']=='POST') {
    	session_destroy();
        header("Location:login.php");
        exit;
    	
    }
?>

<!DOCTYPE HTML>
<html>
<head>
</head> 
<body>
<div>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <p>Logged In</p><br>
    Welcome user <?php echo $_SESSION["fname"].' (@'.$_SESSION["uname"].').';?><br><br>
    <input type="submit" id="submit" name="submit" value="Logout">
</form>
</div>
</body>
</html>