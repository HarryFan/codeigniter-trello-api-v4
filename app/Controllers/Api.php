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

  protected function setCorsHeaders()
  {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 7200');
  }

  /**
   * 處理 CORS 預檢請求
   */
  public function options()
  {
    $this->setCorsHeaders();
    return $this->response->setStatusCode(200);
  }

  /**
   * 處理帶參數的 CORS 預檢請求
   */
  public function options_wildcard()
  {
    $this->setCorsHeaders();
    return $this->response->setStatusCode(200);
  }

  /**
   * 取得指定清單下所有卡片
   * GET /lists/{listId}/cards
   */
  public function cardsByList($listId)
  {
    $this->setCorsHeaders();
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
    $this->setCorsHeaders();
    $cardModel = new CardModel();
    $data = $this->request->getJSON(true);
    $insert = [
      'list_id' => $listId,
      'title' => $data['title'] ?? '',
      'description' => $data['description'] ?? '',
      'position' => $data['position'] ?? 0,
      'deadline' => $data['deadline'] ?? null
    ];
    
    if (!$cardModel->insert($insert)) {
      return $this->fail($cardModel->errors());
    }
    
    $cardId = $cardModel->getInsertID();
    $card = $cardModel->find($cardId);
    return $this->respondCreated($card);
  }

  /**
   * 編輯卡片
   * PUT /cards/{cardId}
   */
  public function updateCard($cardId)
  {
    $this->setCorsHeaders();
    $cardModel = new CardModel();
    $data = $this->request->getJSON(true);
    $card = $cardModel->find($cardId);
    
    if (!$card) {
      return $this->failNotFound('卡片不存在');
    }
    
    $update = [
      'title' => $data['title'] ?? $card['title'],
      'description' => $data['description'] ?? $card['description'],
      'list_id' => $data['list_id'] ?? $card['list_id'],
      'position' => $data['position'] ?? $card['position'],
      'deadline' => $data['deadline'] ?? $card['deadline']
    ];
    
    if (!$cardModel->update($cardId, $update)) {
      return $this->fail($cardModel->errors());
    }
    
    $updated = $cardModel->find($cardId);
    return $this->respond(['status' => 'ok', 'data' => $updated]);
  }

  /**
   * 刪除卡片
   * DELETE /cards/{cardId}
   */
  public function deleteCard($cardId)
  {
    $this->setCorsHeaders();
    $cardModel = new CardModel();
    $card = $cardModel->find($cardId);
    if (!$card) {
      return $this->failNotFound('卡片不存在');
    }
    $cardModel->delete($cardId);
    return $this->respondDeleted(['id' => $cardId]);
  }

  /**
   * 取得指定看板的所有清單（lists）
   * GET /boards/{boardId}/lists
   * @param int $boardId
   * @return \CodeIgniter\HTTP\Response
   */
  public function listsByBoard($boardId)
  {
    $this->setCorsHeaders();
    // 範例資料，可串接資料庫替換
    $lists = [
      [
        'id' => 1,
        'boardId' => (int)$boardId,
        'title' => '待辦',
        'order' => 1,
        'cards' => [
          [
            'id' => '10',
            'list_id' => '1',
            'title' => '１１１１２',
            'description' => '２２２２２３３３３',
            'position' => '0',
            'listTitle' => '待辦'
          ],
          [
            'id' => '11',
            'list_id' => '1',
            'title' => 'VVV',
            'description' => 'DDD',
            'position' => '0',
            'listTitle' => '待辦'
          ],
          [
            'id' => '12',
            'list_id' => '1',
            'title' => 'DDD',
            'description' => 'ASDSA',
            'position' => '0',
            'listTitle' => '待辦'
          ]
        ]
      ],
      [
        'id' => 2,
        'boardId' => (int)$boardId,
        'title' => '進行中',
        'order' => 2,
        'cards' => []
      ],
      [
        'id' => 3,
        'boardId' => (int)$boardId,
        'title' => '已完成',
        'order' => 3,
        'cards' => []
      ]
    ];

    return $this->response->setJSON([
      'status' => 'ok',
      'data' => $lists
    ]);
  }

  /**
   * 新增清單
   * POST /boards/{boardId}/lists
   */
  public function createList($boardId)
  {
    $this->setCorsHeaders();
    $listModel = new ListModel();
    $data = $this->request->getJSON(true);
    $insert = [
      'board_id' => $boardId,
      'title' => $data['title'] ?? '',
      'position' => $data['position'] ?? 0
    ];
    
    if (!$listModel->insert($insert)) {
      return $this->fail($listModel->errors());
    }
    
    $listId = $listModel->getInsertID();
    $list = $listModel->find($listId);
    return $this->respondCreated($list);
  }

  /**
   * 取得所有看板資料
   * @return \CodeIgniter\HTTP\Response
   */
  public function boards()
  {
    $this->setCorsHeaders();
    // 範例回傳空陣列，可依需求串接資料庫
    return $this->response->setJSON([
      'status' => 'ok',
      'data' => []
    ]);
  }

  /**
   * API 健康檢查
   * GET /api
   */
  public function index()
  {
    $this->setCorsHeaders();
    return $this->respond(['status' => 'ok', 'message' => 'API service 運作正常']);
  }
}
