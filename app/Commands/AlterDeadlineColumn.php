<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AlterDeadlineColumn extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:alter_deadline';
    protected $description = '將 cards 表的 deadline 欄位從 date 修改為 datetime 類型';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        try {
            // 執行 ALTER TABLE 命令
            $sql = "ALTER TABLE `cards` MODIFY COLUMN `deadline` datetime DEFAULT NULL";
            $db->query($sql);
            
            CLI::write('成功將 cards 表的 deadline 欄位修改為 datetime 類型', 'green');
        } catch (\Exception $e) {
            CLI::error('執行失敗: ' . $e->getMessage());
        }
    }
}
