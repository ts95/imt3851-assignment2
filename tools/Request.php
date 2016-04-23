<?php
namespace Tools;

class Request {

    /**
     * Converts the PHP array argument to JSON and
     * echoes it to the response.
     * Once the JSON has been echoed the request will
     * terminate.
     */
    public static function json($array) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($array);
        exit(0);
    }

    /**
     * Check if the request is an XHR request.
     */
    public static function isXHR() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Check the request method.
     */
    public static function method($type) {
        return $_SERVER['REQUEST_METHOD'] == strtoupper($type);
    }
}