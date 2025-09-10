<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $profile_image;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $email, $password) {
        // Check if username or email already exists
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username OR email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return false; // User already exists
        }

        // Insert new user
        $query = "INSERT INTO " . $this->table_name . " (username, email, password_hash) VALUES (:username, :email, :password_hash)";
        $stmt = $this->conn->prepare($query);
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);

        return $stmt->execute();
    }

    public function login($username, $password) {
        $query = "SELECT id, username, email, password_hash, profile_image FROM " . $this->table_name . " WHERE username = :username OR email = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password_hash'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->profile_image = $row['profile_image'];
                return true;
            }
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT id, username, email, profile_image FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->profile_image = $row['profile_image'];
            return true;
        }
        return false;
    }

    public function updateProfile($id, $username, $email, $profile_image = null) {
        if ($profile_image) {
            $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, profile_image = :profile_image WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email WHERE id = :id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        
        if ($profile_image) {
            $stmt->bindParam(':profile_image', $profile_image);
        }

        return $stmt->execute();
    }

    public function updateProfileWithPassword($id, $username, $email, $new_password, $profile_image = null) {
        if ($profile_image) {
            $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, password_hash = :password_hash, profile_image = :profile_image WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, password_hash = :password_hash WHERE id = :id";
        }
        
        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        
        if ($profile_image) {
            $stmt->bindParam(':profile_image', $profile_image);
        }

        return $stmt->execute();
    }
}
?>
