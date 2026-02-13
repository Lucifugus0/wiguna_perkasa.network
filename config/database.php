<?php
// ==================== DATABASE CONFIGURATION ====================
// Wrapper untuk existing database connection
// TIDAK mengubah koneksi yang sudah ada

// Load existing connection
require_once __DIR__ . '/../inc/koneksi.php';

// Database helper class (optional - untuk future use)
class DB {
    private static $connection = null;

    public static function getConnection() {
        global $koneksi;

        if (self::$connection === null) {
            self::$connection = $koneksi;
        }

        return self::$connection;
    }

    public static function query($sql) {
        return self::getConnection()->query($sql);
    }

    public static function escape($string) {
        return self::getConnection()->real_escape_string($string);
    }
}

// Export global $koneksi untuk backward compatibility
return $koneksi;
