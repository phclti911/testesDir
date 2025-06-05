<?php
require_once '../config/database.php';
require_once __DIR__ . '/../Models/CandidaturaModel.php';
class CandidaturaController {
    public function create() {
        if (ob_get_level()) ob_clean();
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if ($data === null) {
            http_response_code(400); // JSON inválido
            return;
        }
        if (empty($data['id_vaga']) || empty($data['id_pessoa'])) {
            http_response_code(400);
            return;
        }
        $id = !empty($data['id']) ? $data['id'] : $this->generateUUID();
        $id_vaga = $data['id_vaga'];
        $id_pessoa = $data['id_pessoa'];
        $candidaturaModel = new CandidaturaModel();
        if ($candidaturaModel->existeId($id)) {
            http_response_code(400);
            $candidaturaModel->fechar();
            return;
        }
        if ($candidaturaModel->existeDuplicidade($id_vaga, $id_pessoa)) {
            http_response_code(400);
            $candidaturaModel->fechar();
            return;
        }
        $vaga = $candidaturaModel->buscarVaga($id_vaga);
        $pessoa = $candidaturaModel->buscarPessoa($id_pessoa);
        if (!$vaga || !$pessoa) {
            http_response_code(404);
            $candidaturaModel->fechar();
            return;
        }
        $score = $this->calcularScore($vaga['nivel'], $pessoa['nivel'], $vaga['localizacao'], $pessoa['localizacao']);
        $res = $candidaturaModel->inserir($id, $id_vaga, $id_pessoa, $score);
        if ($res) {
            http_response_code(201);
            header('Content-Type: application/json');
            echo json_encode([
                'mensagem' => 'Candidatura cadastrada com sucesso. Consulte o banco para validar o registro.',
                'id' => $id,
                'score' => $score
            ]);
        } else {
            http_response_code(422);
        }
        $candidaturaModel->fechar();
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
    private function calcularScore($nivelVaga, $nivelPessoa, $localVaga, $localPessoa) {
        // N = 100 - 25 * (NV - NC)
        $N = 100 - 25 * ((int)$nivelVaga - (int)$nivelPessoa);
        $D = $this->calcularDistancia($localVaga, $localPessoa);
        $score = $N + $D / 2;
        return (int)round($score);
    }
    private function calcularDistancia($origem, $destino) {
        // Grafo das distâncias
        $mapa = [
            'A' => ['B' => 5],
            'B' => ['A' => 5, 'C' => 7, 'D' => 3],
            'C' => ['B' => 7, 'E' => 4],
            'D' => ['B' => 3, 'E' => 10, 'F' => 8],
            'E' => ['C' => 4, 'D' => 10],
            'F' => ['D' => 8],
        ];
        // Dijkstra
        $dist = [];
        $visitados = [];
        foreach ($mapa as $k => $v) $dist[$k] = INF;
        $dist[$origem] = 0;
        while (true) {
            $min = INF;
            $n = null;
            foreach ($dist as $vertice => $d) {
                if (!isset($visitados[$vertice]) && $d < $min) {
                    $min = $d;
                    $n = $vertice;
                }
            }
            if ($n === null) break;
            foreach ($mapa[$n] as $vizinho => $peso) {
                if ($dist[$n] + $peso < $dist[$vizinho]) {
                    $dist[$vizinho] = $dist[$n] + $peso;
                }
            }
            $visitados[$n] = true;
        }
        $d = isset($dist[$destino]) ? $dist[$destino] : INF;
        if ($d <= 5) return 100;
        if ($d <= 10) return 75;
        if ($d <= 15) return 50;
        if ($d <= 20) return 25;
        return 0;
    }
}
