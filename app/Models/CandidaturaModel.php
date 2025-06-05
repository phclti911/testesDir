<?php
require_once __DIR__ . '/../../config/database.php';
class CandidaturaModel {
    private $mysqli;
    public function __construct() {
        $dbConfig = require __DIR__ . '/../../config/database.php';
        $this->mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['dbname'], $dbConfig['port']);
    }
    public function inserir($id, $id_vaga, $id_pessoa, $score) {
        $stmt = $this->mysqli->prepare('INSERT INTO candidaturas (id, id_vaga, id_pessoa, score) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('sssi', $id, $id_vaga, $id_pessoa, $score);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    public function existeId($id) {
        $stmt = $this->mysqli->prepare('SELECT id FROM candidaturas WHERE id = ?');
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows > 0;
        $stmt->close();
        return $existe;
    }
    public function existeDuplicidade($id_vaga, $id_pessoa) {
        $stmt = $this->mysqli->prepare('SELECT id FROM candidaturas WHERE id_vaga = ? AND id_pessoa = ?');
        $stmt->bind_param('ss', $id_vaga, $id_pessoa);
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows > 0;
        $stmt->close();
        return $existe;
    }
    public function buscarVaga($id_vaga) {
        $result = $this->mysqli->query("SELECT nivel, localizacao FROM vagas WHERE id = '".$this->mysqli->real_escape_string($id_vaga)."'");
        return $result ? $result->fetch_assoc() : null;
    }
    public function buscarPessoa($id_pessoa) {
        $result = $this->mysqli->query("SELECT nivel, localizacao FROM pessoa WHERE id = '".$this->mysqli->real_escape_string($id_pessoa)."'");
        return $result ? $result->fetch_assoc() : null;
    }
    public function fechar() {
        $this->mysqli->close();
    }
}
