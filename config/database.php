<?php
class Database {
    // Update these values to match your Coolify DB settings
    private $host = "coolify-db";       // Service name of your MySQL in Coolify
    private $port = "3307";             // MySQL default port (or whatever you mapped)
    private $db_name = "notes_system";  // Your database name
    private $username = "mysql";        // Your MySQL username
    private $password = "QffIbyRUoqDcxGsJw3wA5T1WvWZlFR7OAzj4FkgczEWkcHBwwo8wivZLAd0BtCIN"; // Your MySQL password

    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
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
