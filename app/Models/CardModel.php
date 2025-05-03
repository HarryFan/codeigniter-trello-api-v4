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
    protected $useTimestamps = false; // 禁用時間戳記功能，避免嘗試插入 created_at 和 updated_at
    
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
    protected $dates = ['deadline']; // 只保留 deadline 作為日期欄位
}
