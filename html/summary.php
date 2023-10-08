<?php
require_once "php/loginCheck.php";
?>

<!--form page-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
	<title>Form</title>

    <link rel="stylesheet" href="css/summary.css">
    <link rel="stylesheet" href="css/dl.css">
</head>

<?php
require_once "php/Connection.php";
require_once "php/SelectQuery.php";
require_once "php/ChangeQuery.php";

$connection = Connection::getConnection();

$name = $_GET["name"];
$phone1 = $_GET["phone1"];
$model = $_GET["model"];
$note = $_GET["note"];
$email = $_GET["email"];

$problems = $_GET["problems"];

$price_estimate = $_GET["price_estimate"];
$pickup_estimate = $_GET["pickup_estimate"];
// convert date format
$pickup_estimate = date("d-m-Y", strtotime($pickup_estimate));

$job_id = $_GET["job_id"];
?>

<body>
<div class="summary-section">
    <div>Job Number: <?php echo sprintf("E%05d", $job_id)?></div>
    <div><?php echo date('d-m-Y');?></div>
</div>

<dl class="summary-section">
    <div><dt>Name</dt><dd><?php echo $name;?></dd></div>
    <div><dt>Phone</dt><dd><?php echo $phone1;?></dd></div>
    <div><dt>Email</dt><dd><?php echo $email;?></dd></div>
    <div><dt>Model</dt><dd><?php echo $model;?></dd></div>
</dl>

<div class="summary-section">
    <div>Problems:</div>
    <?php
        for($i = 0; $i < count($problems); $i++) {
            echo "<div>{$problems[$i]}</div>";
        }
    ?>
</div>

<div class="summary-section">
    <div>Note: <?php echo $note;?></div>
</div>

<dl class="summary-section">
    <dt>Estimated price</dt><dd><?php echo $price_estimate;?></dd></div>
    <dt>Estimated pickup date</dt><dd><?php echo $pickup_estimate;?></dd></div>
</dl>

<ul class="summary-section">
    <li>Backup your data first, we are not responsible for any possible data loss during repair.</li>
    <li>All repair services come with a 3-month warranty.</li>
    <li>Warranty for repaired parts only.</li>
    <li>Physical and water damages are not covered.</li>
</ul>

<div class="summary-section">
    <a href="form.php">New form</a>
    <a href="menu.php">Leave</a>
</div>
</body>
</html>