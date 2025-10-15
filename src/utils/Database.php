<?php
// Clase para gestionar la conexión a la base de datos de manera segura con PDO
class Database {
    private $pdo;
    private $statement;

    // Constructor: Establece la conexión a la base de datos
    public function __construct() {
        // Asegúrate de que las constantes de config/config.php estén cargadas y sean correctas
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Manejo de errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo de fetch por defecto
            PDO::ATTR_EMULATE_PREPARES => false, // Desactiva la emulación de prepared statements
        ];
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // 🚨 CAMBIO CRÍTICO: Imprimir el error de la base de datos 🚨
            http_response_code(500);
            die('Error de Conexión a la Base de Datos: ' . $e->getMessage());
        }
    }

    // Prepara una consulta SQL
    public function query($sql) {
        $this->statement = $this->pdo->prepare($sql);
    }

    // Enlaza un valor a un parámetro en la consulta preparada
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->statement->bindValue($param, $value, $type);
    }

    // Ejecuta la consulta preparada
    public function execute() {
        return $this->statement->execute();
    }

    // Obtiene un único registro
    public function single() {
        $this->execute();
        return $this->statement->fetch();
    }

    // Obtiene múltiples registros
    public function resultSet() {
        $this->execute();
        return $this->statement->fetchAll();
    }

    // Obtiene el ID del último registro insertado
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
?>