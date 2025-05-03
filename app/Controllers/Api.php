<?php
/**
 * @file Api.php
 * @desc Trello API Controller (CodeIgniter 4)
 */
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CardModel;
use App\Models\ListModel;
use App\Models\BoardModel;

/**
 * Trello RESTful API Controller
 *
 * @desc 支援卡片、清單、看板 CRUD
 */
class Api extends ResourceController
{
  use ResponseTrait;

  /**
   * 取得指定清單下所有卡片
   * GET /lists/{listId}/cards
   */
  public function cardsByList($listId)
  {
    $cardModel = new CardModel();
    $cards = $cardModel->where('list_id', $listId)->findAll();
    return $this->respond($cards);
  }

  /**
   * 新增卡片
   * POST /lists/{listId}/cards
   */
  public function createCard($listId)
  {
    $cardModel = new CardModel();
    $data = $this->request->getJSON(true);
    $insert = [
      'list_id' => $listId,
      'title' => $data['title'] ?? '',
      'description' => $data['description'] ?? '',
      'date' => $data['date'] ?? null,
      'deadline' => $data['deadline'] ?? null,
      'created_at' => date('Y-m-d H:i:s'),
    ];
    $cardId = $cardModel->insert($insert, true);
    $card = $cardModel->find($cardId);
    return $this->respondCreated($card);
  }

  /**
   * 編輯卡片
   * PUT /cards/{cardId}
   */
  public function updateCard($cardId)
  {
    $cardModel = new CardModel();
    $data = $this->request->getJSON(true);
    $card = $cardModel->find($cardId);
    if (!$card) {
      return $this->failNotFound('卡片不存在');
    }
    $update = [
      'title' => $data['title'] ?? $card['title'],
      'description' => $data['description'] ?? $card['description'],
      'date' => $data['date'] ?? $card['date'],
      'deadline' => $data['deadline'] ?? $card['deadline'],
      'list_id' => $data['list_id'] ?? $card['list_id'],
      'position' => $data['position'] ?? $card['position'],
    ];
    $cardModel->update($cardId, $update);
    $updated = $cardModel->find($cardId);
    return $this->respond($updated);
  }

  /**
   * 刪除卡片
   * DELETE /cards/{cardId}
   */
  public function deleteCard($cardId)
  {
    $cardModel = new CardModel();
    $card = $cardModel->find($cardId);
    if (!$card) {
      return $this->failNotFound('卡片不存在');
    }
    $cardModel->delete($cardId);
    return $this->respondDeleted(['id' => $cardId]);
  }

  /**
   * 取得指定看板下所有清單
   * GET /boards/{boardId}/lists
   */
  public function listsByBoard($boardId)
  {
    $listModel = new ListModel();
    $lists = $listModel->where('board_id', $boardId)->findAll();
    return $this->respond($lists);
  }

  /**
   * 新增清單
   * POST /boards/{boardId}/lists
   */
  public function createList($boardId)
  {
    $listModel = new ListModel();
    $data = $this->request->getJSON(true);
    $insert = [
      'board_id' => $boardId,
      'title' => $data['title'] ?? '',
      'created_at' => date('Y-m-d H:i:s'),
    ];
    $listId = $listModel->insert($insert, true);
    $list = $listModel->find($listId);
    return $this->respondCreated($list);
  }

  /**
   * API 健康檢查
   * GET /api
   */
  public function index()
  {
    return $this->respond(['status' => 'ok', 'message' => 'API service 運作正常']);
  }
}
