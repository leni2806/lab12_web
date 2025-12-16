<?php
class Database {
    private $conn;
    
    public function __construct() {
        // Gunakan konstanta yang sudah didefinisikan di config.php
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $user = defined('DB_USER') ? DB_USER : 'root';
        $pass = defined('DB_PASS') ? DB_PASS : '';
        $dbname = defined('DB_NAME') ? DB_NAME : 'latihan_oop';
        
        // Buat koneksi
        $this->conn = new mysqli($host, $user, $pass, $dbname);
        
        // Cek koneksi
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }
    
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>