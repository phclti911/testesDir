<?php
class HomeController {
    public function index() {
        require_once '../app/Models/HelloModel.php';
        $model = new HelloModel();
        $message = $model->getMessage();
        ob_start();
        require '../app/Views/home.php';
        return ob_get_clean();
    }
}
