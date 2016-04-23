<?php

session_start();

class Auth {
    public static function login($user) {
        $_SESSION['auth'] = $user;
    }

    public static function logout() {
        unset($_SESSION['auth']);
    }

    public static function check() {
        return isset($_SESSION['auth']);
    }

    public static function user() {
        if (self::check()) {
            return $_SESSION['auth'];
        } else {
            throw new Exception("Not logged in");
            die();
        }
    }

    public static function userJson() {
        return json_encode(array_diff_key((array) self::user(), array_flip(['password'])));
    }
}
