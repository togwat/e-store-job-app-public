<?php
require_once "php/loginCheck.php";
?>

<!--menu page-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Menu</title>
</head>

<body>
<a class="menu-link" id="ml-form" href="form.php">New form</a>
<?php
// admin(2) only links
if($_SESSION["userType"] == 2) {
	echo "<a class='menu-link' id='ml-jobs' href='jobs.php'>Jobs</a>";
	echo "<a class='menu-link' id='ml-models' href='models.php'>Models</a>";
}
?>
</body>
</html>
