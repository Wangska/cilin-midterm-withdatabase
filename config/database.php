<?php
class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $this->host     = getenv("DB_HOST") ?: "coolify-db";
        $this->port     = getenv("DB_PORT") ?: "3306";
        $this->db_name  = getenv("DB_NAME") ?: "notes_system";
        $this->username = getenv("DB_USER") ?: "mysql";
        $this->password = getenv("DB_PASS") ?: "QffIbyRUoqDcxGsJw3wA5T1WvWZlFR7OAzj4FkgczEWkcHBwwo8wivZLAd0BtCIN";
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
