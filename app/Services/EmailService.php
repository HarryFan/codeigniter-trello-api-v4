<?php

namespace App\Services;

use CodeIgniter\Email\Email;

class EmailService
{
    private $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();
        
        // 配置郵件設定
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => 'smtp.gmail.com',
            'SMTPUser' => 'your-email@gmail.com',
            'SMTPPass' => 'your-app-password',
            'SMTPPort' => 587,
            'SMTPCrypto' => 'tls',
            'mailType' => 'html',
            'charset' => 'utf-8',
            'wordWrap' => true,
        ];
        
        $this->email->initialize($config);
    }

    /**
     * 發送任務到期提醒郵件
     */
    public function sendTaskDueReminder($userEmail, $taskInfo)
    {
        $this->email->setFrom('your-email@gmail.com', '任務管理系統');
        $this->email->setTo($userEmail);
        $this->email->setSubject('任務即將到期提醒');
        
        $message = $this->buildTaskDueEmail($taskInfo);
        $this->email->setMessage($message);
        
        return $this->email->send();
    }

    /**
     * 構建任務到期提醒郵件內容
     */
    private function buildTaskDueEmail($taskInfo)
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2196f3; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .task-info { margin: 20px 0; padding: 15px; background: white; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>任務即將到期提醒</h2>
                </div>
                <div class='content'>
                    <p>您好，</p>
                    <p>您有一個任務即將到期，詳細信息如下：</p>
                    <div class='task-info'>
                        <p><strong>任務標題：</strong>{$taskInfo['title']}</p>
                        <p><strong>所屬清單：</strong>{$taskInfo['list_title']}</p>
                        <p><strong>任務描述：</strong>{$taskInfo['description']}</p>
                        <p><strong>截止時間：</strong>{$taskInfo['deadline']}</p>
                    </div>
                    <p>請及時處理此任務。</p>
                </div>
                <div class='footer'>
                    <p>此郵件由系統自動發送，請勿直接回覆。</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
