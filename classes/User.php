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
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            mysqli_stmt_close($stmt);
            return false; // User already exists
        }
        mysqli_stmt_close($stmt);

        // Insert new user
        $query = "INSERT INTO " . $this->table_name . " (username, email, password_hash) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $query);
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password_hash);

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    public function login($username, $password) {
        $query = "SELECT id, username, email, password_hash, profile_image FROM " . $this->table_name . " WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password_hash'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->profile_image = $row['profile_image'];
                mysqli_stmt_close($stmt);
                return true;
            }
        }
        mysqli_stmt_close($stmt);
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT id, username, email, profile_image FROM " . $this->table_name . " WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->profile_image = $row['profile_image'];
            mysqli_stmt_close($stmt);
            return true;
        }
        mysqli_stmt_close($stmt);
        return false;
    }

    public function updateProfile($id, $username, $email, $profile_image = null) {
        if ($profile_image) {
            $query = "UPDATE " . $this->table_name . " SET username = ?, email = ?, profile_image = ? WHERE id = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $profile_image, $id);
        } else {
            $query = "UPDATE " . $this->table_name . " SET username = ?, email = ? WHERE id = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $id);
        }

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    public function updateProfileWithPassword($id, $username, $email, $new_password, $profile_image = null) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        if ($profile_image) {
            $query = "UPDATE " . $this->table_name . " SET username = ?, email = ?, password_hash = ?, profile_image = ? WHERE id = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $password_hash, $profile_image, $id);
        } else {
            $query = "UPDATE " . $this->table_name . " SET username = ?, email = ?, password_hash = ? WHERE id = ?";
            $stmt = mysqli_prepare($this->conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $password_hash, $id);
        }

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
}
?>
