<?php
include_once "../header.php";
?>
<title>IPS Importer</title>
<div class="page-header">
    <h4>IPS Importer</h4>    
</div>

<?php
if ($_POST == null) {
    ?>
    <form action="ips-importer.php" method="post" enctype="multipart/form-data">
        Select ".csv" to upload:
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" value="Update" name="submit">
    </form>
    <?php
} else {

    if (isset($_POST["submit"])) {
        unlink("test.csv");
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "test.csv");
    }

    $myfile = fopen("test.csv", "r") or die("Unable to open file!");
    $counter = 0;
    while (!feof($myfile)) {
        $counter++;
        $line = fgets($myfile);
        $line_arr = explode(";", $line);

        if ($counter == 1)
            continue;

        if (strlen($line_arr[1]) > 5 && strlen($line_arr[2]) > 5) {
            $query = mysql_query("Update `customers` set `ip_address` = '" . $line_arr[2] . "' where `mac_address` = '" . $line_arr[1] . "'");
            echo $line_arr[1] . " " . $line_arr[2] . " " . $query . "<br>";
        }
    }
    fclose($myfile);
}
?>

<?php
include_once "../footer.php";
?>