<?php
namespace App\Models;
use CodeIgniter\Model;

/**
 * 清單 Model
 */
class ListModel extends Model
{
  protected $table = 'lists';
  protected $primaryKey = 'id';
  protected $useAutoIncrement = true;
  protected $returnType = 'array';
  protected $allowedFields = [
    'board_id', 'title', 'position'
  ];
  
  protected $useTimestamps = false; // 禁用時間戳記功能，避免嘗試插入 created_at 和 updated_at
  
  protected $validationRules = [
    'board_id' => 'required|numeric',
    'title' => 'required|min_length[1]|max_length[255]',
    'position' => 'permit_empty|numeric'
  ];
}
