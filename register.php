<?php
require "db.php";
mysqli_report(MYSQLI_REPORT_OFF);
/** @var mysqli $connect */
$err = "";
if(isset($_POST["submit"])) {
    $newuser = $_POST["username"];
    $pass = $_POST["pass"];
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $sql = "select * from users where username =  ?";
    $stmt = mysqli_prepare($connect, $sql) or die(mysqli_error($connect));
    mysqli_stmt_bind_param($stmt, "s", $newuser);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (empty($_POST["username"]) || empty($_POST["pass"])) {
        $err = "please enter a username or password";
    }elseif ($_POST["pass"] != $_POST["confirmpass"]){
        $err = "different passwords";
    }
    elseif (mysqli_num_rows($result) > 0){
    $err = "username already taken";
    }
    else{
    $sql = "insert into users (username, password) values (?, ?)";
    $stmt = mysqli_prepare($connect, $sql)or die(mysqli_error($connect));
    mysqli_stmt_bind_param($stmt, "ss" ,$newuser, $hash);
    mysqli_stmt_execute($stmt);
    header("location: login.php");
    exit();
}
}
?>

<html>
<?php
echo $err;  ?>
<form method="post">
    <input type="text" name="username" placeholder="username">
    <input type="password" name="pass" placeholder="password">
    <input type="password" name="confirmpass" placeholder="confirmpass">
    <button name="submit">submit</button>
</form>
</html>