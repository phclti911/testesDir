<?php
require_once __DIR__ . '/../Models/RankingModel.php';
require_once __DIR__ . '/../Views/JsonView.php';
class RankingController {
    public function rankingPorVaga($idVaga) {
        $rankingModel = new RankingModel();
        if (!$rankingModel->existeCandidaturaParaVaga($idVaga)) {
            JsonView::render([], 404);
            $rankingModel->fechar();
            return;
        }
        $candidatos = $rankingModel->rankingPorVaga($idVaga);
        JsonView::render($candidatos, 200);
        $rankingModel->fechar();
    }
}
