<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CardModel;
use App\Models\UserModel;
use App\Models\NotificationModel;

class Notifications extends ResourceController
{
  use ResponseTrait;
  
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
   * 獲取即將到期任務
   */
  public function upcoming()
  {
    $this->setCorsHeaders();
    
    // 從 token 獲取用戶 ID (簡化示例)
    $userId = 1; // 實際應用中從 JWT 或 session 獲取
    $minutes = $this->request->getGet('minutes') ?? 30; // 未來 30 分鐘內到期的任務
    
    $model = new CardModel();
    $tasks = $model->getUpcomingTasks($userId, $minutes);
    
    return $this->respond([
      'status' => 'success',
      'data' => $tasks
    ]);
  }
  
  /**
   * 更新通知設定
   */
  public function settings()
  {
    $this->setCorsHeaders();
    
    // 從 token 獲取用戶 ID (簡化示例)
    $userId = 1; // 實際應用中從 JWT 或 session 獲取
    $data = $this->request->getJSON(true);
    
    $notificationModel = new NotificationModel();
    
    // 更新用戶通知設定
    $notificationModel->updateSettings($userId, [
      'browser_enabled' => $data['browser'] ?? true,
      'email_enabled' => $data['email'] ?? true,
      'polling_interval' => $data['pollingInterval'] ?? 30000,
      'email_lead_time' => $data['emailLeadTime'] ?? 60
    ]);
    
    return $this->respond([
      'status' => 'success',
      'message' => '通知設定已更新'
    ]);
  }
  
  /**
   * 測試電子郵件發送
   */
  public function testEmail()
  {
    $this->setCorsHeaders();
    
    $data = $this->request->getJSON(true);
    $email = $data['email'] ?? '';
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return $this->respond([
        'status' => 'error',
        'message' => '請提供有效的電子郵件地址'
      ], 400);
    }
    
    // 發送測試郵件
    $emailService = \Config\Services::email();
    
    $emailService->setFrom('noreply@trelloclone.com', 'Trello Clone');
    $emailService->setTo($email);
    $emailService->setSubject('測試通知 - Trello Clone');
    $emailService->setMessage('這是一封測試郵件，確認您的 Trello Clone 通知功能正常運作。');
    
    if ($emailService->send()) {
      return $this->respond([
        'status' => 'success',
        'message' => '測試郵件已發送'
      ]);
    } else {
      return $this->respond([
        'status' => 'error',
        'message' => '郵件發送失敗: ' . $emailService->printDebugger(['headers'])
      ], 500);
    }
  }
}
