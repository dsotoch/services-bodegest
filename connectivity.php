<?php
require_once __DIR__ . '/config.php';
class Connectivity
{
    private $servername = SERVERNAME;
    private $username = USERNAMEDB;
    private $password = PASSWORDDB;
    private $database = DATABASE;
    public $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die("El servidor MySQL no está activo o la conexión falló. Verifica que el servidor esté en ejecución antes de continuar.");
        }

        $this->conn->autocommit(false);
    }


    public function isConnectionActive()
    {
        return $this->conn->ping();
    }

    public function getMysql($query)
    {
        $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
        $response = $this->conn->query($query);
        if ($response->num_rows >0) {
            $this->conn->commit();
            return mysqli_fetch_assoc($response);
        } else {
            $this->conn->rollback();
            return array();
        }
    }
}
