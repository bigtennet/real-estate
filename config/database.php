<?php
class Database {
    private $host = 'db.pxxl.pro';
    private $port = '23588';
    private $db_name = 'db_ed51f1cd';
    private $username = 'user_9cf68f87';
    private $password = 'a713b2de02a475f4e4609ce9ecb25128';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>
