<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Services\EmailService;
use App\Models\CardModel;
use App\Models\UserModel;

class Notifications extends ResourceController
{
    use ResponseTrait;
    
    private $emailService;

    public function __construct()
    {
        // 設置 CORS 頭
        header('Access-Control-Allow-Origin: http://localhost:5173');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-User-Id');
        header('Access-Control-Allow-Credentials: true');
        
        // 初始化郵件服務
        $this->emailService = new EmailService();
        
        // 如果是 OPTIONS 請求，直接返回
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }
    }

    /**
     * 處理 OPTIONS 請求
     */
    public function options()
    {
        return $this->response->setStatusCode(204);
    }

    /**
     * 獲取即將到期的任務並發送郵件通知
     */
    public function upcoming()
    {
        try {
            $cardModel = new CardModel();
            $userId = $this->request->getHeader('X-User-Id')->getValue();
            
            if (!$userId) {
                return $this->fail('未提供用戶 ID');
            }

            // 獲取30分鐘內到期的任務
            $tasks = $cardModel->getUpcomingTasks($userId, 30);
            
            // 如果有即將到期的任務，發送郵件通知
            if (!empty($tasks)) {
                $userModel = new UserModel();
                $user = $userModel->find($userId);
                
                if ($user && $user['email']) {
                    foreach ($tasks as $task) {
                        $this->emailService->sendTaskDueReminder($user['email'], $task);
                    }
                }
            }
            
            return $this->respond([
                'status' => 'success',
                'data' => $tasks
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Notifications] 獲取到期任務失敗: ' . $e->getMessage());
            return $this->fail('獲取到期任務失敗');
        }
    }
}
