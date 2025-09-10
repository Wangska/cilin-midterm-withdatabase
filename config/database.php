<?php
class Database {
    private $host = "coolify-db";
    private $db_name = "notes_system";
    private $username = "mysql";
    private $password = "QffIbyRUoqDcxGsJw3wA5T1WvWZlFR7OAzj4FkgczEWkcHBwwo8wivZLAd0BtCIN";
    private $port = 3307;
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            if (strpos($_SERVER['REQUEST_URI'], 'api/') !== false) {
                // For API calls, don't output HTML
                throw $exception;
            } else {
                echo "Connection error: " . $exception->getMessage();
            }
        }
        return $this->conn;
    }
}
?>
