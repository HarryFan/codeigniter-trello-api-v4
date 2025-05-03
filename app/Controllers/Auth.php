<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class Auth extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        // CORS headers 已經由 CorsFilter 處理
    }

    /**
     * 處理 OPTIONS 請求
     */
    public function options()
    {
        return $this->response->setStatusCode(204);
    }

    /**
     * 登入
     */
    public function login()
    {
        $json = $this->request->getJSON(true);
        $email = $json['email'] ?? '';
        $password = $json['password'] ?? '';

        // 檢查是否為預設帳密
        if ($email === 'harry750110@gmail.com' && $password === '12345678') {
            $response = [
                'id' => 1,
                'email' => $email,
                'name' => '王小明',
                'token' => 'dummy_token_' . time()  // 實際應用中應該使用 JWT
            ];
            return $this->respond($response);
        }

        return $this->failUnauthorized('帳號或密碼錯誤');
    }
}
