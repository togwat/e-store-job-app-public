<?php
session_start();
?>

<!--login page-->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login</title>
</head>

<?php
require_once "php/Connection.php";
require_once "php/SelectQuery.php";

// unset login
unset($_SESSION["userType"]);
// only run when form is submitted
if(isset($_POST["fi-username"]) && isset($_POST["fi-password"])) {
	// set up user object
	$username = $_POST["fi-username"];
	$password = $_POST["fi-password"];

	// check user with database
	$connection = Connection::getConnection();

	// determine user type
	$login_arguments = array($username, $password);
	$login_query = new SelectQuery("SELECT user_type FROM users WHERE username = ? AND password = SHA2(?, 256);", $login_arguments, $connection);
	$login_result = $login_query->performQuery();
	
	// login fail(no results found), alert and stay
	if(count($login_result) == 0) {
	    echo "<script type='text/javascript'>alert('Incorrect username or password.')</script>";
	}
	// set user type, go to menu page
	else {
		// store user to session
		$_SESSION["userType"] = $login_result[0];
	    echo "<script type='text/javascript'>window.location.href='menu.php'</script>";
	}
}
?>

<body>
<form class="form" id="f-login" method="post">
	<h1 class="form-header">E-Store Repair Jobs</h1>

	<label class="form-label" for="fi-username">Username</label>
	<input class="form-input" type="text" maxlength="64" name="fi-username" id="fi-username" required>

	<label class="form-label" for="fi-password">Password</label>
	<input class="form-input" type="password" name="fi-password" id="fi-password" required>

	<input class="form-button" id="fb-submit" type="submit" value="Login">
</form>
</body>
</html>
