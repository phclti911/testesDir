<?php
require_once __DIR__ . '/../../config/database.php';
class VagaModel {
    private $mysqli;
    public function __construct() {
        $dbConfig = require __DIR__ . '/../../config/database.php';
        $this->mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['dbname'], $dbConfig['port']);
    }
    public function inserir($id, $empresa, $titulo, $descricao, $localizacao, $nivel) {
        $stmt = $this->mysqli->prepare('INSERT INTO vagas (id, empresa, titulo, descricao, localizacao, nivel) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $id, $empresa, $titulo, $descricao, $localizacao, $nivel);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    public function existeId($id) {
        $stmt = $this->mysqli->prepare('SELECT id FROM vagas WHERE id = ?');
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows > 0;
        $stmt->close();
        return $existe;
    }
    public function fechar() {
        $this->mysqli->close();
    }
}
