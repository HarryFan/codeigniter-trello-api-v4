<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\NotificationModel;

class SendNotifications extends BaseCommand
{
  protected $group = 'Notifications';
  protected $name = 'notifications:send';
  protected $description = '發送任務到期電子郵件通知';
  
  public function run(array $params)
  {
    $notificationModel = new NotificationModel();
    $emailService = \Config\Services::email();
    
    // 獲取所有需要發送郵件的用戶和任務
    $notifications = $notificationModel->getUsersForEmailNotification();
    
    if (empty($notifications)) {
      CLI::write('沒有需要發送的通知', 'yellow');
      return;
    }
    
    CLI::write('開始發送 ' . count($notifications) . ' 個通知...', 'green');
    
    // 在日誌中輸出所有即將發送的通知
    $logFile = WRITEPATH . 'logs/email_notifications.log';
    $logData = "=============== " . date('Y-m-d H:i:s') . " ===============\n";
    $logData .= "找到 " . count($notifications) . " 個需要發送的通知\n\n";
    
    $successCount = 0;
    $errorCount = 0;
    
    // 按用戶分組
    $groupedNotifications = [];
    foreach ($notifications as $notification) {
      $userId = $notification['id'];
      if (!isset($groupedNotifications[$userId])) {
        $groupedNotifications[$userId] = [
          'user' => [
            'id' => $userId,
            'email' => $notification['email'],
            'name' => $notification['name']
          ],
          'tasks' => []
        ];
      }
      
      $groupedNotifications[$userId]['tasks'][] = [
        'id' => $notification['card_id'],
        'title' => $notification['title'],
        'deadline' => $notification['deadline'],
        'list_title' => $notification['list_title'],
        'board_id' => $notification['board_id']
      ];
    }
    
    // 向每個用戶發送一封包含多個任務的郵件
    foreach ($groupedNotifications as $userId => $userData) {
      $user = $userData['user'];
      $tasks = $userData['tasks'];
      
      // 構建郵件內容
      $message = "親愛的 {$user['name']},\n\n";
      $message .= "您有以下任務即將到期：\n\n";
      
      foreach ($tasks as $task) {
        $deadline = date('Y/m/d H:i', strtotime($task['deadline']));
        $message .= "- {$task['title']} (在 {$task['list_title']} 清單中)\n";
        $message .= "  截止時間：{$deadline}\n\n";
      }
      
      $message .= "請及時處理這些任務。\n\n";
      $message .= "此致,\n您的 Trello Clone 團隊";
      
      // 記錄每個用戶的郵件內容
      $logData .= "發送給: {$user['name']} <{$user['email']}>\n";
      $logData .= "郵件主題: 任務即將到期提醒 - Trello Clone\n";
      $logData .= "郵件內容: \n$message\n\n";
      
      // 發送郵件
      $emailService->clear();
      $emailService->setFrom('noreply@trelloclone.com', 'Trello Clone');
      $emailService->setTo($user['email']);
      $emailService->setSubject('任務即將到期提醒 - Trello Clone');
      $emailService->setMessage($message);
      
      try {
        if ($emailService->send(false)) { // 傳入 false 表示不自動清除
          $successCount++;
          // 更新最後發送時間
          $notificationModel->updateLastEmailSent($userId);
          CLI::write("成功發送郵件給 {$user['email']}", 'green');
          $logData .= "狀態: 成功發送\n";
        } else {
          $errorCount++;
          $debugInfo = $emailService->printDebugger(['headers']);
          CLI::write("發送郵件失敗 {$user['email']}: " . $debugInfo, 'red');
          $logData .= "狀態: 發送失敗\n";
          $logData .= "錯誤信息: " . $debugInfo . "\n";
        }
      } catch (\Exception $e) {
        $errorCount++;
        CLI::write("發送郵件異常 {$user['email']}: " . $e->getMessage(), 'red');
        $logData .= "狀態: 發送異常\n";
        $logData .= "錯誤信息: " . $e->getMessage() . "\n";
      }
      
      $logData .= "----------------------------------------\n";
    }
    
    // 寫入日誌
    $logData .= "發送完成: {$successCount} 成功, {$errorCount} 失敗\n";
    $logData .= "================================================\n\n";
    file_put_contents($logFile, $logData, FILE_APPEND);
    
    CLI::write("發送完成: {$successCount} 成功, {$errorCount} 失敗", 'yellow');
    CLI::write("詳細日誌已記錄到 " . $logFile, 'blue');
  }
}
