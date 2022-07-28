<?php

    session_start();

    $sqluser = "user";
    $sqlpassword = "password";

    $sqldatabase = "login";

    $post = $_SERVER['REQUEST_METHOD']=='POST';
 
    if ($post) {
        $empty_fields = false;
        if(
            empty($_POST['uname'])||
            empty($_POST['fname'])||
            empty($_POST['lname'])||
            empty($_POST['email'])||
            empty($_POST['pass'])||
            empty($_POST['repass'])
        ) $empty_fields = true;

        else {
            $unmatch = preg_match('/^[A-Za-z][A-Za-z0-9_]{3,}$/', $_POST['uname']);
            $fnmatch = preg_match('/^[A-Za-z]+$/', $_POST['fname']);
            $lnmatch = preg_match('/^[A-Za-z]+$/', $_POST['lname']);
            $emmatch = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST['email']);
            $pmatch = preg_match('/.{5,}/',$_POST['pass']);
            $peq = $_POST['pass']==$_POST['repass'];
            if($unmatch&&$fnmatch&&$lnmatch&&$emmatch&&$pmatch&&$peq) {
                try {
                    $pdo = new PDO("mysql:host=localhost;dbname=".$sqldatabase,$sqluser,$sqlpassword);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    exit($e->getMessage());
                }
                $st = $pdo->prepare('SELECT * FROM list WHERE user_name=?');
                $st->execute(array($_POST['uname']));
                $uname_err = $st->fetch() != null;
                $st = $pdo->prepare('SELECT * FROM list WHERE email=?');
                $st->execute(array($_POST['email']));
                $email_err = $st->fetch() != null;
                if(!$uname_err&&!$email_err) {
                    $stmt = 'INSERT INTO list(user_name,first_name,last_name,email,password) VALUES (?,?,?,?,?)';
                    $pdo->prepare($stmt)->execute(array(
                        $_POST['uname'],
                        $_POST['fname'],
                        $_POST['lname'],
                        $_POST['email'],
                        $_POST['pass']
                    ));
                    $_SESSION["uname"] = $_POST["uname"];
                    $_SESSION["pass"] = $_POST["pass"];
                    $_SESSION["fname"] = $_POST["fname"];
                    header("Location:success.php");
                    exit;
                }
            }
        }
    }
?>

<!DOCTYPE HTML>
<html>
<head>
</head> 
<body>
<div>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <p>SignUp</p>
    <?php
    echo 'Username<br><input type="text" name="uname" value="" placeholder="Username"><br>';
    if($post&&!$empty_fields&&!$unmatch) echo '<span>Username can contain alphabet letters, numbers and underscore(_), but must begin with a letter. It must be at least 4 character long.<br></span>';
    if(!empty($uname_err)&&$uname_err) echo '<span>Username taken. Try another username.</span>';
    echo '<br>Name<br><input type="text" name="fname" value="" placeholder="First Name"><br>';
    echo '<input type="text" name="lname" value="" placeholder="Last Name"><br>';
    if($post&&!$empty_fields&&!($lnmatch&&$fnmatch)) echo '<span>Name can only contain alphabet letters.<br></span>';
    echo '<br>E-mail<br><input type="text" name="email" value="" placeholder="email@example.com"><br>';
    if(!empty($email_err)&&$email_err) echo '<span>Email already registered. Enter another email.</span>';
    if($post&&!$empty_fields&&!$emmatch) echo '<span>Email must be of format example@site.domain<br></span>';
    echo '<br>Password<br><input type="password" name="pass" placeholder="Password"><br>';
    echo '<input type="password" name="repass" placeholder="Retype password">';
    if($post&&!$empty_fields&&!$pmatch) echo '<span>Password must be at least 5 character long</span>';
    if($post&&!$empty_fields&&$pmatch&&!$peq) echo '<span>Password don\'t match</span><br>';
    if($post &&$empty_fields) echo "<br><span>Please fill all the fields completely.</span><br>";
    ?>
    <br>
    <input type="submit" id="submit" value="SignUp"><br><br>
    Already have a account? <a href="login.php">LogIn</a>.<br><br>
</form>
</div>
</body>
</html>