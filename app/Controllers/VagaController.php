<?php
require_once '../config/database.php';
class VagaController {
    public function create() {
        // Limpa qualquer saída antes de enviar headers
        if (ob_get_level()) ob_clean();
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if ($data === null) {
            http_response_code(400); // JSON inválido
            return;
        }
        // Validação dos campos obrigatórios (id agora opcional)
        $camposObrigatorios = ['empresa', 'titulo', 'localizacao', 'nivel'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($data[$campo])) {
                http_response_code(422); // Unprocessable Entity
                return;
            }
        }
        // Validação de localizacao e nivel (apenas letras A-F e nível numérico >= 1)
        $localizacoesValidas = ['A', 'B', 'C', 'D', 'E', 'F'];
        if (!in_array($data['localizacao'], $localizacoesValidas, true)) {
            http_response_code(422);
            return;
        }
        if (!is_numeric($data['nivel']) || (int)$data['nivel'] < 1) {
            http_response_code(422);
            return;
        }
        // Gera UUID se não vier id
        $id = !empty($data['id']) ? $data['id'] : $this->generateUUID();
        // Verificar unicidade do id
        $dbConfig = require '../config/database.php';
        $mysqli = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass'], $dbConfig['dbname'], $dbConfig['port']);
        if ($mysqli->connect_errno) {
            http_response_code(500);
            return;
        }
        $check = $mysqli->prepare('SELECT id FROM vagas WHERE id = ?');
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
        $stmt = $mysqli->prepare('INSERT INTO vagas (id, empresa, titulo, descricao, localizacao, nivel) VALUES (?, ?, ?, ?, ?, ?)');
        $descricao = isset($data['descricao']) ? $data['descricao'] : null;
        $stmt->bind_param('sssssi', $id, $data['empresa'], $data['titulo'], $descricao, $data['localizacao'], $data['nivel']);
        if ($stmt->execute()) {
            http_response_code(201); // Created
            header('Content-Type: application/json');
            echo json_encode([
                'mensagem' => 'Vaga cadastrada com sucesso. Consulte o banco para validar o registro.',
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
