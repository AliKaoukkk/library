<?php
session_start();
require "db.php";
mysqli_report(MYSQLI_REPORT_OFF);

if(isset($_POST["submit"])){
    if(isset($_POST["forget"])){
        setcookie("username", $_POST["username"], time() -100);
    }
    $name = $_POST["username"];
    $sql = "SELECT * FROM users WHERE username = '$name'" ;
    $result = mysqli_query($connect, $sql) or die(mysqli_error($connect));
    $row = mysqli_fetch_array($result);
    mysqli_close($connect);
    if(mysqli_num_rows($result) == 0){
       echo "wrong";
    }else{
        if(isset($_POST["remember"])){
            setcookie("username", $_POST["username"], time()  + 3600 *24 * 7);
        }
        if(password_verify($_POST["pass"],$row["password"])){
            $_SESSION["username"] = $_POST["username"];
            header("Location: books.php");
            exit;
        }
        echo "wrong pass";
    }

}
?>
<?php
$val = "";
if (isset($_COOKIE["username"])) {
    $val = $_COOKIE["username"];
}
?>

<html>
<form method="post">
    <input type="text" name="username" value = "<?php echo $val?>">
    <input type="password" name="pass">
    <button name="submit">submit</button>
    <input type="checkbox" name="remember"> Remember me
    <input type="checkbox" name="forget"> forget me
</form>
    <form method="post" action="register.php">
        <button name="register">signup</button>
    </form>
</html>
