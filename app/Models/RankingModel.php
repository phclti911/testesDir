<?php
require_once __DIR__ . '/../../config/database.php';
class RankingModel {
    private $mysqli;
    public function __construct() {
        $dbConfig = require __DIR__ . '/../../config/database.php';
        $this->mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['dbname'], $dbConfig['port']);
    }
    public function rankingPorVaga($idVaga) {
        $stmt = $this->mysqli->prepare('
            SELECT p.id, p.nome, p.profissao, p.localizacao, p.nivel, c.score
            FROM candidaturas c
            INNER JOIN pessoa p ON c.id_pessoa = p.id
            WHERE c.id_vaga = ?
            ORDER BY c.score DESC
        ');
        $stmt->bind_param('s', $idVaga);
        $stmt->execute();
        $result = $stmt->get_result();
        $candidatos = [];
        while ($row = $result->fetch_assoc()) {
            $candidatos[] = $row;
        }
        $stmt->close();
        return $candidatos;
    }
    public function existeCandidaturaParaVaga($idVaga) {
        $stmt = $this->mysqli->prepare('SELECT id FROM candidaturas WHERE id_vaga = ? LIMIT 1');
        $stmt->bind_param('s', $idVaga);
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
