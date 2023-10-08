<?php
// Use on admin only pages. Redirect to menu if regular(1).
if($_SESSION["userType"] == 1) {
    header("Location: menu.php");
}
?>