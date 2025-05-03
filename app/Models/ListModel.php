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
  protected $allowedFields = [
    'board_id', 'title', 'created_at'
  ];
  protected $useTimestamps = false;
}
