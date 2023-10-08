<?php
class User {
    // singleton User
    private static $_username;
    private static $_password;
    private static $_user_type;

    private static $_user;

    private function __construct($username, $password) {
        self::$_username = $username;
        self::$_password = $password;
    }

    public static function getUser($username, $password) {
        if(!isset(self::$_user)) {
            self::$_user = new User($username, $password);
        }

        return self::$_user;
    }

    function getUsername() {
        return self::$_username;
    }

    function getPassword() {
        return self::$_password;
    }

    function getUserType() {
        return self::$_user_type;
    }

    function setUserType($type) {
        self::$_user_type = $type;
    }
}
?>