<?php
require_once '../config/database.php';
require_once __DIR__ . '/../Models/RankingModel.php';
class RankingController {
    public function rankingPorVaga($idVaga) {
        $rankingModel = new RankingModel();
        if (!$rankingModel->existeCandidaturaParaVaga($idVaga)) {
            http_response_code(404);
            $rankingModel->fechar();
            return;
        }
        $candidatos = $rankingModel->rankingPorVaga($idVaga);
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($candidatos);
        $rankingModel->fechar();
    }
}
