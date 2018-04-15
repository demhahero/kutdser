<?php
include_once "header.php";
?>

<?php
$date = new DateTime();

if (isset($_POST["username"])) {
    $target_file_name = "";
    if (file_exists($_FILES['uploadedfile']['tmp_name']) || is_uploaded_file($_FILES['uploadedfile']['tmp_name'])) {
        $target_dir = "uploadedfiles/";
        $target_file = $target_dir . $date->getTimestamp() . "_" . basename($_FILES["uploadedfile"]["name"]);
        $target_file_name = $date->getTimestamp() . "_" . basename($_FILES["uploadedfile"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["uploadedfile"]["tmp_name"], $target_file)) {
                
            } else {
                echo "<div class='alert alert-error'>Sorry, there was an error uploading your file.</div>";
            }
        }
    }

    $result = mysql_query("INSERT INTO `users` (
				`username` ,
				`password` ,
				`ip`,
				`mac`,
				`uploadedfile`,
				`notes`
				)
				VALUES (
				'" . $_POST["username"] . "', '" . $_POST["password"] . "', '" . $_POST["ip"] . "', '" . $_POST["mac"] . "', '" . $target_file_name . "', '" . $_POST["notes"] . "'
				);");

    if ($result) {
        header("Location: index.php");
        die();
        //echo "<div class='alert alert-success'>done</div>";
    }
}

function randomString($length = 6) {
    $str = "";
    $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
}

$result = mysql_query("
    SHOW TABLE STATUS LIKE 'users'
");
$data = mysql_fetch_assoc($result);
$next_increment = $data['Auto_increment'];
?>

<title>Create Router</title>
<div class="page-header">
    <h4>Create Router</h4>    
</div>
<div class="alert alert-info">
    <strong>ID:</strong> ID: router_<?php echo $next_increment; ?>
</div>

<form class="register-form" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="email">Username:</label>
        <input type="text" name="username" value="<?php echo randomString(8); ?>" class="form-control" placeholder="Username"/>
    </div>
    <div class="form-group">
        <label for="email">Password:</label>
        <input type="text" name="password" value="<?php echo randomString(8); ?>" class="form-control" placeholder="Password"/>
    </div>
    <div class="form-group">
        <label for="email">ip:</label>
        <input type="text" name="ip" class="form-control" placeholder="ip address"/>
    </div>
    <div class="form-group">
        <label for="email">mac address:</label>
        <input type="text" name="mac" class="form-control" placeholder="mac address"/>
    </div>
    <div class="form-group">
        <label for="email">uploadedfile:</label>
        <input type="file" name="uploadedfile" class="form-control" placeholder="Password"/>
    </div>
    <div class="form-group">
        <label for="email">notes:</label>
        <textarea name="notes" class="form-control" placeholder="notes"></textarea>
    </div>
    <input type="submit" class="btn btn-default" value="create">
</form>

<?php
include_once "footer.php";
?>