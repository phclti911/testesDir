<?php
require_once '../config/database.php';
class CandidatoController {
    public function create() {
        if (ob_get_level()) ob_clean();
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if ($data === null) {
            http_response_code(400);
            return;
        }
        $camposObrigatorios = ['nome', 'profissao', 'localizacao', 'nivel'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($data[$campo])) {
                http_response_code(422);
                return;
            }
        }
        $localizacoesValidas = ['A', 'B', 'C', 'D', 'E', 'F'];
        if (!in_array($data['localizacao'], $localizacoesValidas, true)) {
            http_response_code(422);
            return;
        }
        if (!is_numeric($data['nivel']) || (int)$data['nivel'] < 1) {
            http_response_code(422);
            return;
        }
        $id = !empty($data['id']) ? $data['id'] : $this->generateUUID();
        $dbConfig = require '../config/database.php';
        $mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['dbname'], $dbConfig['port']);
        if ($mysqli->connect_errno) {
            http_response_code(500);
            return;
        }
        $check = $mysqli->prepare('SELECT id FROM pessoa WHERE id = ?');
        $check->bind_param('s', $id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            http_response_code(422);
            $check->close();
            $mysqli->close();
            return;
        }
        $check->close();
        $stmt = $mysqli->prepare('INSERT INTO pessoa (id, nome, profissao, localizacao, nivel) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $id, $data['nome'], $data['profissao'], $data['localizacao'], $data['nivel']);
        if ($stmt->execute()) {
            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso. Consulte o banco para validar o registro.',
                'id' => $id
            ]);
        } else {
            http_response_code(422);
        }
        $stmt->close();
        $mysqli->close();
    }
    private function generateUUID() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
