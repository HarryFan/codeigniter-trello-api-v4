<?php
namespace App\Models;
use CodeIgniter\Model;

/**
 * 卡片 Model
 */
class CardModel extends Model
{
    protected $table = 'cards';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['list_id', 'title', 'description', 'position', 'deadline'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'list_id' => 'required|numeric',
        'title' => 'required|min_length[1]|max_length[255]',
        'description' => 'permit_empty',
        'position' => 'permit_empty|numeric',
        'deadline' => 'permit_empty|valid_date'
    ];
    
    protected $dateFormat = 'datetime';
    protected $useSoftDeletes = false;
    
    // 日期欄位
    protected $dates = ['created_at', 'updated_at', 'deadline'];
    
    public function getUpcomingTasks($userId, $minutes)
    {
        $now = date('Y-m-d H:i:s');
        $future = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));
        
        return $this->db->table('cards c')
            ->select('c.id, c.title, c.deadline, l.title as list_title, b.id as board_id')
            ->join('lists l', 'l.id = c.list_id')
            ->join('boards b', 'b.id = l.board_id')
            ->where('b.user_id', $userId)
            ->where('c.deadline >=', $now)
            ->where('c.deadline <=', $future)
            ->get()
            ->getResultArray();
    }
}
