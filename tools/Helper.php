<?php
namespace Tools;

class Helper {

    /**
     * Returns a rough estimate of the number of
     * words in a piece of text (delimited by spaces).
     */
    public static function wordCount($text) {
        return count(explode(' ', $text));
    }

    /**
     * Generates a random file name based on the
     * microtime() function.
     */
    public static function randomFilename($ext) {
        return (microtime(true) * 10000) . '.' . $ext;
    }

    /**
     * Returns current time as a string in a format
     * that's suitable for SQL columns.
     */
    public static function now() {
        return date('Y-m-d H:i:s');
    }
}