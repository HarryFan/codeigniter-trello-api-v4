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
    'title', 'created_at'
  ];
  protected $useTimestamps = false;
}
