<?php
require_once '../config/database.php';
require_once __DIR__ . '/../Models/PessoaModel.php';
class PessoaController {
    public function create() {
        if (ob_get_level()) ob_clean();
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if ($data === null) {
            http_response_code(400); // JSON inválido
            return;
        }
        // Validação dos campos obrigatórios (id agora opcional)
        $camposObrigatorios = ['nome', 'profissao', 'localizacao', 'nivel'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($data[$campo])) {
                http_response_code(422); // Unprocessable Entity
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
        // Gera UUID se não vier id
        $id = !empty($data['id']) ? $data['id'] : $this->generateUUID();
        $pessoaModel = new PessoaModel();
        if ($pessoaModel->existeId($id)) {
            http_response_code(422);
            $pessoaModel->fechar();
            return;
        }
        $res = $pessoaModel->inserir($id, $data['nome'], $data['profissao'], $data['localizacao'], $data['nivel']);
        if ($res) {
            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso. Consulte o banco para validar o registro.',
                'id' => $id
            ]);
        } else {
            http_response_code(422);
        }
        $pessoaModel->fechar();
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
