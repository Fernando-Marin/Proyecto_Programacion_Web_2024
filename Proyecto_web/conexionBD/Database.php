<?php
class Database
{
    private $host = "localhost";        // Dirección del servidor
    private $db_name = "tutoria";      // Nombre de la base de datos
    private $username = "root";         // Usuario de la base de datos
    private $password = "12345";        // Contraseña del usuario
    private $conn;                      // Instancia de conexión
    private static $instance;           // Instancia Singleton

    // Constructor privado para evitar múltiples instancias
    private function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->db_name;charset=utf8", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Error al conectar a la base de datos: " . $exception->getMessage();
        }
    }

    // Método para obtener la instancia única de la conexión
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Método para obtener la conexión PDO
    public function getConnection()
    {
        return $this->conn;
    }
}
