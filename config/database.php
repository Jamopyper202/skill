<?php
/**
 * Database Configuration
 * SkillSwap - Digital Skill Marketplace
 */

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'skillswap');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

if (!class_exists('Database')) {

    class Database
    {
        private static ?PDO $instance = null;

        public static function getConnection(): PDO
        {
            if (self::$instance === null) {
                try {
                    $dsn = "mysql:host=" . DB_HOST .
                           ";dbname=" . DB_NAME .
                           ";charset=" . DB_CHARSET;

                    self::$instance = new PDO(
                        $dsn,
                        DB_USER,
                        DB_PASS,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false,
                        ]
                    );

                } catch (PDOException $e) {
                    die("Database connection failed: " . $e->getMessage());
                }
            }

            return self::$instance;
        }

        private function __clone() {}

        public function __wakeup()
        {
            throw new Exception("Cannot unserialize singleton");
        }

        
    }
}