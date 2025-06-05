<?php
require_once '../config/database.php';
class RankingController {
    public function rankingPorVaga($idVaga) {
        $dbConfig = require '../config/database.php';
        $mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['dbname'], $dbConfig['port']);
        if ($mysqli->connect_errno) {
            http_response_code(500);
            return;
        }
        // Verifica se existe pelo menos uma candidatura para a vaga
        $check = $mysqli->prepare('SELECT id FROM candidaturas WHERE id_vaga = ? LIMIT 1');
        $check->bind_param('s', $idVaga);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 0) {
            http_response_code(404);
            $check->close();
            $mysqli->close();
            return;
        }
        $check->close();
        $stmt = $mysqli->prepare('
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
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($candidatos);
        $stmt->close();
        $mysqli->close();
    }
}
