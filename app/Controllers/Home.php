<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    /**
     * 處理所有 OPTIONS 請求，回應 204 並讓 CORS filter 正常運作
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function options()
    {
        return $this->response->setStatusCode(204);
    }
}
