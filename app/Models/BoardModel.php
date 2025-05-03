<?php
namespace App\Models;
use CodeIgniter\Model;

/**
 * 看板 Model
 */
class BoardModel extends Model
{
    protected $table = 'boards';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'user_id', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * 重設看板為預設狀態
     */
    public function reset($boardId)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();

            // 1. 檢查看板是否存在
            $board = $this->find($boardId);
            if (!$board) {
                throw new \Exception('看板不存在');
            }

            // 2. 刪除所有相關資料
            $listModel = new \App\Models\ListModel();
            $cardModel = new \App\Models\CardModel();

            // 先刪除所有卡片
            $lists = $listModel->where('board_id', $boardId)->findAll();
            foreach ($lists as $list) {
                $cardModel->where('list_id', $list['id'])->delete();
            }

            // 再刪除所有清單
            $listModel->where('board_id', $boardId)->delete();

            // 3. 插入預設清單
            $lists = [
                ['board_id' => $boardId, 'title' => '待處理', 'position' => 0],
                ['board_id' => $boardId, 'title' => '進行中', 'position' => 1],
                ['board_id' => $boardId, 'title' => '已完成', 'position' => 2]
            ];
            $listModel->insertBatch($lists);
            
            // 4. 取得插入的清單 ID
            $insertedLists = $listModel->where('board_id', $boardId)
                                     ->orderBy('position', 'ASC')
                                     ->findAll();
            
            // 5. 插入預設卡片
            $now = date('Y-m-d H:i:s');
            $cards = [
                [
                    'list_id' => $insertedLists[0]['id'],
                    'title' => '生產線效率優化',
                    'description' => '分析現有生產流程，找出瓶頸並提出改善方案',
                    'deadline' => '2025-05-10 23:59:59',
                    'position' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'list_id' => $insertedLists[0]['id'],
                    'title' => '品質管理系統更新',
                    'description' => '導入新的品質檢測標準，更新 SOP 文件',
                    'deadline' => '2025-05-15 23:59:59',
                    'position' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'list_id' => $insertedLists[1]['id'],
                    'title' => '倉儲自動化專案',
                    'description' => '規劃自動倉儲系統，提升物料管理效率',
                    'deadline' => '2025-05-20 23:59:59',
                    'position' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'list_id' => $insertedLists[2]['id'],
                    'title' => '工安改善計畫',
                    'description' => '完成工廠安全設施升級，更新緊急應變程序',
                    'deadline' => '2025-05-01 23:59:59',
                    'position' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];
            $cardModel->insertBatch($cards);

            $db->transComplete();

            return $db->transStatus();
        } catch (\Exception $e) {
            log_message('error', '[BoardModel] 重設看板失敗: ' . $e->getMessage());
            return false;
        }
    }
}
