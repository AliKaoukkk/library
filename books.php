<?php
session_start();
require "db.php";
/** @var mysqli $connect */
mysqli_report(MYSQLI_REPORT_OFF);
if(!isset($_SESSION["username"])){
    header("location: login.php");
    exit;
}
if(isset($_POST["delete"])){
    $sql = "delete from books where id =". $_POST["delete"];
    mysqli_query($connect, $sql) or die(mysqli_error($connect));
}
if(isset($_POST["insert"])){

    $_name = $_POST["author"];
    $_title = $_POST["title"];
    $_copies = $_POST["copies"];
    $_owner = $_SESSION["username"];
    $sql = "insert into books (title, author, copies, owner) values ('$_title','$_name',$_copies, '$_owner')";
    mysqli_query($connect, $sql) or die(mysqli_error($connect));
}

echo "hello " . $_SESSION["username"];
echo "<br>";

$sql = "select * from books";
$result = mysqli_query($connect, $sql) or die(mysqli_error($connect));
echo "<table border = 1>";
echo "<tr>";
echo "<th>title</th> ";
echo "<th>Author</th>";
echo "<th>copies</th>";
echo "</tr>";
while($row = mysqli_fetch_array($result)){
    echo "<tr>";
    echo "<td>$row[title]</td>";
    echo "<td> $row[author]</td>";
    echo "<td>$row[copies]</td>";
    echo "<td><form method='post'><button name='delete' value='$row[id]'>delete</button> </form> </td>";
    echo "</tr>";
}
echo "</table>";

mysqli_close($connect);
?>
<html>
<form method="post">
    <br>
    <input type="text" placeholder="title" name="title">
    <input type="text" placeholder="Author" name="author">
    <input type="text" placeholder="copies" name="copies">
    <button name="insert">insert</button>
</form>
</html>
