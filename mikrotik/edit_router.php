<?php
include_once "header.php";
?>

<?php
$date = new DateTime();
if(isset($_POST["username"])){

	$target_file_name ="";
	if(file_exists($_FILES['uploadedfile']['tmp_name']) || is_uploaded_file($_FILES['uploadedfile']['tmp_name'])){
		$target_dir = "uploadedfiles/";
		$target_file = $target_dir . $date->getTimestamp() . "_" . basename($_FILES["uploadedfile"]["name"]);
		$target_file_name = $date->getTimestamp() . "_" . basename($_FILES["uploadedfile"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["uploadedfile"]["tmp_name"], $target_file)) {

			} else {
				echo "Sorry, there was an error uploading your file.";
			}
		}	
	}

	if($target_file_name == ""){
		$query = mysql_query("select * from `users` where `router_id`='".$_GET["router_id"]."'");
		$row =  mysql_fetch_array($query);
		$target_file = $row["uploadedfile"];
	}
	
	$result = mysql_query("Update `users` set
				`username` = '".$_POST["username"]."',
				`password` = '".$_POST["password"]."',
				`ip` = '".$_POST["ip"]."',
				`mac` = '".$_POST["mac"]."',
				`uploadedfile` = '".$target_file_name."',
				`notes` = '".$_POST["notes"]."'
				where `router_id` = '".$_GET["router_id"]."';
				");
	
	if($result)
		echo "<div class='alert alert-success'>done</div>";
}

$query = mysql_query("select * from `users` where `router_id`='".$_GET["router_id"]."'");
$row =  mysql_fetch_array($query);
?>

<title>Edit Router</title>
<div class="page-header">
    <h4>Edit Router</h4>    
</div>

<form class="register-form" method="post" enctype="multipart/form-data">

	<div class="form-group">
		<label for="email">Username:</label>
		<input type="text" name="username" value="<?=$row["username"];?>" class="form-control" placeholder="Username"/>
	</div>
	<div class="form-group">
		<label for="email">Password:</label>
		<input type="text" name="password" value="<?=$row["password"];?>" class="form-control" placeholder="Password"/>
	</div>
	<div class="form-group">
		<label for="email">ip:</label>
		<input type="text" name="ip" value="<?=$row["ip"];?>" class="form-control" placeholder="ip address"/>
	</div>
	<div class="form-group">
		<label for="email">mac address:</label>
		<input type="text" name="mac" value="<?=$row["mac"];?>" class="form-control" placeholder="mac address"/>
	</div>
	<div class="form-group">
		<label for="email">uploadedfile:</label>
		<input type="file" name="uploadedfile" class="form-control" placeholder="Password"/>
	</div>
	<div class="form-group">
		<label for="email">notes:</label>
		<textarea name="notes" class="form-control" placeholder="notes"><?=$row["notes"];?></textarea>
	</div>
    <input type="submit" class="btn btn-default" value="update">
</form>

<?php
include_once "footer.php";
?>