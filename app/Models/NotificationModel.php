<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
  protected $table = 'notification_settings';
  protected $primaryKey = 'user_id';
  protected $useAutoIncrement = false;
  protected $returnType = 'array';
  protected $useSoftDeletes = false;
  protected $allowedFields = ['user_id', 'browser_enabled', 'email_enabled', 'polling_interval', 'email_lead_time', 'last_email_sent'];
  protected $useTimestamps = true;
  protected $createdField = 'created_at';
  protected $updatedField = 'updated_at';
  
  public function updateSettings($userId, $settings)
  {
    // 檢查設定是否存在
    $existingSettings = $this->find($userId);
    
    if ($existingSettings) {
      // 更新現有設定
      $this->update($userId, $settings);
    } else {
      // 創建新設定
      $settings['user_id'] = $userId;
      $this->insert($settings);
    }
    
    return true;
  }
  
  public function getUsersForEmailNotification($defaultLeadTime = 60)
  {
    // 確保使用正確的時區
    date_default_timezone_set('Asia/Taipei');
    $now = date('Y-m-d H:i:s');
    
    // 使用更靈活的方式獲取即將到期任務
    // 為每個用戶使用其自訂的提前通知時間
    $results = [];
    
    // 1. 獲取所有啟用了電子郵件通知的用戶
    $users = $this->db->table('notification_settings ns')
      ->select('ns.user_id, ns.email_lead_time, u.email, u.name')
      ->join('users u', 'u.id = ns.user_id')
      ->where('ns.email_enabled', 1)
      ->where('u.email IS NOT NULL')
      ->where("(ns.last_email_sent IS NULL OR DATE_ADD(ns.last_email_sent, INTERVAL 1 MINUTE) < NOW())")
      ->get()
      ->getResultArray();
      
    // 2. 對每個用戶根據其提前時間找出即將到期的任務
    foreach ($users as $user) {
      $leadTime = $user['email_lead_time'] ?? $defaultLeadTime;
      $targetTime = date('Y-m-d H:i:s', strtotime("+{$leadTime} minutes"));
      
      $tasks = $this->db->table('cards c')
        ->select('c.id as card_id, c.title, c.deadline, l.title as list_title, b.id as board_id')
        ->join('lists l', 'l.id = c.list_id')
        ->join('boards b', 'b.id = l.board_id')
        ->where('b.user_id', $user['user_id'])
        ->where('c.deadline >', $now)
        ->where('c.deadline <', $targetTime)
        ->get()
        ->getResultArray();
        
      // 3. 如果有即將到期的任務，將用戶和任務資訊合併
      foreach ($tasks as $task) {
        $results[] = array_merge(
          [
            'id' => $user['user_id'],
            'email' => $user['email'],
            'name' => $user['name']
          ],
          $task
        );
      }
    }
    
    return $results;
  }
  
  public function updateLastEmailSent($userId)
  {
    return $this->update($userId, [
      'last_email_sent' => date('Y-m-d H:i:s')
    ]);
  }
}
