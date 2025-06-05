<?php
require_once '../config/database.php';
require_once __DIR__ . '/../Models/PessoaModel.php';
require_once __DIR__ . '/../Views/JsonView.php';
class PessoaController {
    public function create() {
        if (ob_get_level()) ob_clean();
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if ($data === null) {
            JsonView::render([], 400);
            return;
        }
        $camposObrigatorios = ['nome', 'profissao', 'localizacao', 'nivel'];
        foreach ($camposObrigatorios as $campo) {
            if (empty($data[$campo])) {
                JsonView::render([], 422);
                return;
            }
        }
        $localizacoesValidas = ['A', 'B', 'C', 'D', 'E', 'F'];
        if (!in_array($data['localizacao'], $localizacoesValidas, true)) {
            JsonView::render([], 422);
            return;
        }
        if (!is_numeric($data['nivel']) || (int)$data['nivel'] < 1) {
            JsonView::render([], 422);
            return;
        }
        $id = !empty($data['id']) ? $data['id'] : $this->generateUUID();
        $pessoaModel = new PessoaModel();
        if ($pessoaModel->existeId($id)) {
            JsonView::render([], 422);
            $pessoaModel->fechar();
            return;
        }
        $res = $pessoaModel->inserir($id, $data['nome'], $data['profissao'], $data['localizacao'], $data['nivel']);
        if ($res) {
            JsonView::render([
                'mensagem' => 'Pessoa cadastrada com sucesso. Consulte o banco para validar o registro.',
                'id' => $id
            ], 201);
        } else {
            JsonView::render([], 422);
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
