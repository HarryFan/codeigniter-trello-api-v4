<?php
/**
 * @file Auth.php
 * @desc 認證控制器 (CodeIgniter 4)
 */
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

/**
 * 認證 Controller
 *
 * @desc 支援用戶登入、註冊、登出功能
 */
class Auth extends ResourceController
{
  use ResponseTrait;

  /**
   * 處理 CORS 設置
   */
  protected function setCorsHeaders()
  {
    // 取得前端來源
    $origin = $this->request->getHeaderLine('Origin');
    if (empty($origin)) {
      $origin = '*';
    }
    
    // 設定 CORS 標頭
    $this->response->setHeader('Access-Control-Allow-Origin', $origin);
    $this->response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
    $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
    $this->response->setHeader('Access-Control-Max-Age', '86400');
  }

  /**
   * 處理 CORS 預檢請求
   */
  public function options()
  {
    $this->setCorsHeaders();
    return $this->response->setStatusCode(200);
  }

  /**
   * 用戶登入
   * POST /auth/login
   * 
   * @return \CodeIgniter\HTTP\Response
   */
  public function login()
  {
    $this->setCorsHeaders();
    $data = $this->request->getJSON(true);
    
    // 簡單的測試帳號驗證
    if (
      ($data['email'] === 'harry750110@gmail.com' && $data['password'] === '12345678') ||
      ($data['email'] === 'test@example.com' && $data['password'] === 'password')
    ) {
      $user = [
        'id' => 1,
        'email' => $data['email'],
        'name' => 'Harry',
        'token' => 'test_token_' . md5(time()),
      ];
      
      return $this->respond([
        'status' => 'success',
        'message' => '登入成功',
        'user' => $user
      ]);
    }
    
    return $this->respond([
      'status' => 'error',
      'message' => '郵箱或密碼錯誤'
    ], 401);
  }

  /**
   * 用戶註冊
   * POST /auth/register
   * 
   * @return \CodeIgniter\HTTP\Response
   */
  public function register()
  {
    $this->setCorsHeaders();
    $data = $this->request->getJSON(true);
    
    // 簡單的註冊模擬
    $user = [
      'id' => 1,
      'email' => $data['email'] ?? 'user@example.com',
      'name' => $data['name'] ?? 'New User',
      'token' => 'test_token_' . md5(time()),
    ];
    
    return $this->respond([
      'status' => 'success',
      'message' => '註冊成功',
      'user' => $user
    ]);
  }
  
  /**
   * 用戶登出
   * POST /auth/logout
   * 
   * @return \CodeIgniter\HTTP\Response
   */
  public function logout()
  {
    $this->setCorsHeaders();
    return $this->respond([
      'status' => 'success',
      'message' => '登出成功'
    ]);
  }
}
