<?php
require "auth.php";
require "db.php";
/** @var mysqli $connect */
mysqli_report(MYSQLI_REPORT_OFF);
$message="";

if(isset($_POST["delete"])){
    $delete = $_POST["delete"];
    $user = $_SESSION["username"];
    $sq = "select covers from books where id = ? AND owner = ?";
    $stm = mysqli_prepare($connect, $sq) or die(mysqli_error($connect));
    mysqli_stmt_bind_param($stm, "is", $delete, $user);
    mysqli_stmt_execute($stm) or die(mysqli_stmt_error($stm));
    $result = mysqli_stmt_get_result($stm);
    $row = mysqli_fetch_array($result);
    $covername = "";
    if ($row != null) {
        $covername = $row["covers"];
    }

    $sql = "delete from books where id = ? AND owner = ?";
    $stmt = mysqli_prepare($connect, $sql) or die(mysqli_error($connect));
    mysqli_stmt_bind_param($stmt, "is", $delete,$user);
    mysqli_stmt_execute($stmt) or die(mysqli_stmt_error($stmt));
    if(!empty($covername) && file_exists("covers/". $covername)){
        unlink("covers/". $covername);
    }
}
if(isset($_POST["insert"])){
    $_name = $_POST["author"];
    $_title = $_POST["title"];
    $_copies = $_POST["copies"];
    $_owner = $_SESSION["username"];
    if(empty($_name) || empty($_title)){
        $message = "fill fields please";
    }else {
        if($_FILES["cover"]["error"] == 4) {
            $sql = "insert into books (title, author, copies, owner) values (?,?,?,?)";
            $stmt = mysqli_prepare($connect, $sql) or die(mysqli_error($connect));
            mysqli_stmt_bind_param($stmt, "ssis", $_title, $_name, $_copies, $_owner);
            mysqli_stmt_execute($stmt) or die(mysqli_stmt_error($stmt));
        }else{
            $fileName = $_FILES["cover"]["name"];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if($_FILES["cover"]["error"]!=0){
                $message =  "failed to upload";
            }elseif($ext != "jpg" && $ext !="jpeg" && $ext !="png"){
                    $message = "wrong format";
                } elseif($_FILES["cover"]["size"]>(2 * 1024 * 1024)){
                $message = "file is too big";
            }else{
                $newName = uniqid() . "." . $ext;
                if(move_uploaded_file($_FILES["cover"]["tmp_name"], "covers/". $newName)){
                    $sql = "insert into books (title, author, copies, owner,covers) values (?,?,?,?,?)";
                    $stmt = mysqli_prepare($connect, $sql) or die(mysqli_error($connect));
                    mysqli_stmt_bind_param($stmt, "ssiss", $_title, $_name, $_copies, $_owner, $newName);
                    mysqli_stmt_execute($stmt) or die(mysqli_stmt_error($stmt));

                }else{
                    $message = "failed to move";
                }

            }

        }

    }

}

echo "hello " . htmlspecialchars($_SESSION["username"]);
echo "<br>";

$sql = "select * from books";
$result = mysqli_query($connect, $sql) or die(mysqli_error($connect));
echo "<table border = 1>";
echo "<tr>";
echo "<th>title</th> ";
echo "<th>Author</th>";
echo "<th>copies</th>";
echo "<th>cover</th>";
echo "<th>delete</th>";
echo "</tr>";
while($row = mysqli_fetch_array($result)){
    echo "<tr>";
    echo "<td>". htmlspecialchars($row["title"])."</td>";
    echo "<td>". htmlspecialchars($row["author"]) ."</td>";
    echo "<td>$row[copies]</td>";
    if (empty($row["covers"])) {
        echo "<td>no cover</td>";
    } else {
        $src = "covers/" . htmlspecialchars($row["covers"]);
        $alt = htmlspecialchars($row["title"]);
        echo "<td><img src='$src' alt='$alt' width='60'></td>";
    }
    if($row["owner"] == $_SESSION["username"] ) {
        echo "<td><form method='post'><button name='delete' value='$row[id]'>delete</button> </form> </td>";
    }else{
        echo "<td> </td>";
    }
    echo "</tr>";
}
echo "</table>";
echo $message;


mysqli_close($connect);
?>
<html>
<form method="post" enctype="multipart/form-data">
    <br>
    <input type="text" placeholder="title" name="title">
    <input type="text" placeholder="Author" name="author">
    <input type="text" placeholder="copies" name="copies">
    <input type="file" name="cover">
    <button name="insert">insert</button>
</form>
    <form method="post" action="logout.php">
    <button name="logout">logout</button>
    </form>
</html>
