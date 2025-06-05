<?php
// Roteamento simples para POST e GET
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if (preg_match('#^/vagas$#', $requestUri)) {
        require_once '../app/Controllers/VagaController.php';
        $controller = new VagaController();
        $controller->create();
        exit;
    }
    if (preg_match('#^/pessoas$#', $requestUri)) {
        require_once '../app/Controllers/PessoaController.php';
        $controller = new PessoaController();
        $controller->create();
        exit;
    }
    if (preg_match('#^/candidaturas$#', $requestUri)) {
        require_once '../app/Controllers/CandidaturaController.php';
        $controller = new CandidaturaController();
        $controller->create();
        exit;
    }
}
if ($method === 'GET') {
    if (preg_match('#^/vagas/([a-f0-9\-]+)/candidaturas/ranking$#', $requestUri, $matches)) {
        require_once '../app/Controllers/RankingController.php';
        $controller = new RankingController();
        $controller->rankingPorVaga($matches[1]);
        exit;
    }
    // Exibe Olá, mundo! apenas na raiz
    if ($requestUri === '/' || $requestUri === '/index.php') {
        require_once '../app/Controllers/HomeController.php';
        $controller = new HomeController();
        echo $controller->index();
        exit;
    }
}
// Rota padrão para métodos não tratados
http_response_code(404);
echo 'Rota não encontrada';
