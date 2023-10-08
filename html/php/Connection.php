<?php
class Connection extends mysqli {
    // singleton mysqli
    private static $_host = "localhost";    // localhost for testing
    private static $_username = "root";
    private static $_password = "root";
    private static $_database = "repair_jobs_database";

    private static $_connection;

    private function __construct() {
        # call super
        parent::__construct(self::$_host, self::$_username, self::$_password, self::$_database);

        // connection error
	    if($this->connect_error)
	    {
	       echo "Not connected, error: " . $this->connect_error;
	    }
    }

    public static function getConnection() {
        if(!isset(self::$_connection)) {
            // create connection mysqli
            self::$_connection = new Connection();
        }

        return self::$_connection;
    }
}
?>