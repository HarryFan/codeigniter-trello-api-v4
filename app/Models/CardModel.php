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
  protected $allowedFields = [
    'list_id', 'title', 'description', 'date', 'deadline', 'position', 'created_at'
  ];
  protected $useTimestamps = false;
}
