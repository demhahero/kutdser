<?php
include_once "../header.php";
?>

<?php
$request_id = intval($_GET["request_id"]);
$request = $dbTools->objRequestTools($request_id);

if (isset($_POST["verdict"])) {

    $request->setVerdict($_POST["verdict"]);
    $verdict_date = new DateTime();
    $admin = $dbTools->objAdminTools($admin_id);
    $request->setAdmin($admin);
    $request->setVerdictDate($verdict_date);
    $request_result = $request->doUpdate();

    if ($request_result) {
        echo "<script>window.location.href = \"" . $site_url . "/requests/requests.php\";</script>";
    }
}
?>

<title>Request Details</title>
<div class="page-header">
    <h4>Request Details</h4>    
</div>

<br>


<?php
if ($request->getVerdict() == "") {
    ?>
    <form class="register-form" method="post">
        <div class="form-group">
            <label for="email">Note:</label>
            <?= $request->getNote() ?>
        </div>
        <div class="form-group">
            <label for="email">Verdict:</label>
            <select  name="verdict" class="form-control">
                <option <?php if ($request->getVerdict() == "approve") echo "selected"; ?> value="approve">approve</option>
                <option <?php if ($request->getVerdict() == "disapprove") echo "selected"; ?> value="disapprove">disapprove</option>
            </select>
        </div>
        <input type="submit" class="btn btn-default" value="Submit">
    </form>
    <?php
} else {
    ?>
    <div class="form-group">
        <label for="email">Note:</label>
        <?= $request->getNote() ?>
    </div>
    <div>
        <table class="display table table-striped table-bordered">
            <tr>
                <td>                     
                    "<?= $request->getAdmin()->getUsername() ?>" 
                    <?= $request->getVerdict() ?> 
                    on 
                    <?= $request->getVerdictDate() ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
}
?>

<?php
include_once "../footer.php";
?>