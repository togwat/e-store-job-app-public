<?php
require_once "Connection.php";
require_once "SelectQuery.php";
require_once "ChangeQuery.php";

$connection = Connection::getConnection();


$queryText = "";
$parameters = array();
$queryType = "";

// get parameters
if(isset($_GET["q"]) && isset($_GET["p"]) && isset($_GET["t"])) {
    $queryText = $_GET["q"];
    $parameters = $_GET["p"];
    $queryType = $_GET["t"];
}


// query factory
switch($queryType) {
    case "select":
        $query = new SelectQuery($queryText, $parameters, $connection); // last element is always SQL query
        // return result
        echo json_encode($query->performQuery());        
        break;

    case "change":
        $query = new ChangeQuery($queryText, $parameters, $connection);
        $query->performQuery();
        break;
}
?>