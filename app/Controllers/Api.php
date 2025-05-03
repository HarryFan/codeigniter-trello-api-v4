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
    // 從請求中獲取來源
    $origin = $this->request->getHeaderLine('Origin');
    if (empty($origin)) {
      $origin = '*';
    }
    
    // 設定 CORS 標頭
    $this->response->setHeader('Access-Control-Allow-Origin', $origin);
    $this->response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
    $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
    $this->response->setHeader('Access-Control-Max-Age', '86400');
  }

  /**
   * 處理 CORS 預檢請求
   */
  public function options()
  {
    // 從請求中獲取來源
    $origin = $this->request->getHeaderLine('Origin');
    if (empty($origin)) {
      $origin = '*';
    }
    
    // 設定 CORS 標頭
    $this->response->setHeader('Access-Control-Allow-Origin', $origin);
    $this->response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
    $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
    $this->response->setHeader('Access-Control-Max-Age', '86400');
    
    return $this->response->setStatusCode(200);
  }

  /**
   * 處理帶參數的 CORS 預檢請求
   */
  public function options_wildcard()
  {
    // 從請求中獲取來源
    $origin = $this->request->getHeaderLine('Origin');
    if (empty($origin)) {
      $origin = '*';
    }
    
    // 設定 CORS 標頭
    $this->response->setHeader('Access-Control-Allow-Origin', $origin);
    $this->response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
    $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
    $this->response->setHeader('Access-Control-Max-Age', '86400');
    
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
   * 建立新卡片
   * POST /lists/{listId}/cards
   * @param int $listId
   * @return \CodeIgniter\HTTP\Response
   */
  public function createCard($listId)
  {
    $this->setCorsHeaders();
    
    // 獲取請求資料
    $inputData = $this->request->getJSON(true);
    
    // 驗證 listId 是否存在
    $listModel = new ListModel();
    $listExists = $listModel->find($listId);
    
    if (!$listExists) {
      return $this->response->setStatusCode(404)->setJSON([
        'status' => 'error',
        'message' => "List with ID {$listId} not found."
      ]);
    }
    
    // 取得目前清單中卡片的最高位置，以確保新卡片位置正確
    $cardModel = new CardModel();
    $maxPosition = $cardModel->where('list_id', $listId)
                           ->selectMax('position')
                           ->get()
                           ->getRow();
    
    $position = ($maxPosition && isset($maxPosition->position)) ? $maxPosition->position + 1 : 0;
    
    // 處理日期格式（接受 date 或 deadline 欄位）
    $deadline = null;
    if (isset($inputData['deadline']) && !empty($inputData['deadline'])) {
      $deadline = $inputData['deadline'];
      
      // 處理 ISO 格式的日期時間 (如 2025-05-03T22:22)
      if (strpos($deadline, 'T') !== false) {
        $dateObj = new \DateTime($deadline);
        $deadline = $dateObj->format('Y-m-d H:i:s');
      }
    } elseif (isset($inputData['date']) && !empty($inputData['date'])) {
      $deadline = $inputData['date'];
      
      // 處理 ISO 格式的日期時間
      if (strpos($deadline, 'T') !== false) {
        $dateObj = new \DateTime($deadline);
        $deadline = $dateObj->format('Y-m-d H:i:s');
      }
    }
    
    $data = [
      'list_id'      => $listId,
      'title'        => $inputData['title'] ?? '',
      'description'  => $inputData['description'] ?? '',
      'position'     => $position,
      'deadline'     => $deadline
    ];
    
    // 新增卡片到資料庫
    try {
      $now = new \DateTime();
      $data['created_at'] = $now->format('Y-m-d H:i:s'); // 手動設定創建時間
      $data['updated_at'] = $now->format('Y-m-d H:i:s'); // 手動設定更新時間
      
      $cardModel->insert($data);
      $cardId = $cardModel->getInsertID();
      
      // 獲取完整卡片資訊
      $newCard = $cardModel->find($cardId);
      
      // 獲取清單標題以供前端使用
      $listTitle = $listExists['title'] ?? '';
      
      // 回傳新建立的卡片資料
      $card = [
        'id'          => $cardId,
        'list_id'     => $listId,
        'title'       => $data['title'],
        'description' => $data['description'],
        'position'    => $data['position'],
        'deadline'    => $data['deadline'],
        'created_at'  => $data['created_at'], // 添加创建时间
        'updated_at'  => $data['updated_at'], // 添加更新时间
        'listTitle'   => $listTitle
      ];
      
      return $this->response->setJSON([
        'status' => 'ok',
        'data'   => $card
      ]);
    } catch (\Exception $e) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => $e->getMessage(),
        'debug' => [
          'listId' => $listId,
          'listExists' => $listExists ? 'yes' : 'no',
          'inputData' => $inputData,
          'cardData' => $data
        ]
      ]);
    }
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
    
    // 記錄原始截止日期格式，用於除錯
    $originalDeadline = $data['deadline'] ?? null;
    
    // 處理截止日期，確保日期時間格式正確保存
    $deadline = null;
    if (isset($data['deadline']) && !empty($data['deadline'])) {
      $deadline = $data['deadline'];
      
      // 確保字串格式統一 (如果是 ISO 格式如 2025-05-03T22:22，轉換為 MySQL datetime 格式)
      if (strpos($deadline, 'T') !== false) {
        $dateObj = new \DateTime($deadline);
        $deadline = $dateObj->format('Y-m-d H:i:s');
      }
    }
    
    $update = [
      'title' => $data['title'] ?? $card['title'],
      'description' => $data['description'] ?? $card['description'],
      'list_id' => $data['list_id'] ?? $card['list_id'],
      'position' => $data['position'] ?? $card['position'],
      'deadline' => $deadline ?? $card['deadline']
    ];
    
    if (!$cardModel->update($cardId, $update)) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => '更新卡片失敗',
        'errors' => $cardModel->errors(),
        'debug' => [
          'original_deadline' => $originalDeadline,
          'processed_deadline' => $deadline
        ]
      ]);
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
    
    // 從資料庫獲取清單和卡片
    $listModel = new ListModel();
    $cardModel = new CardModel();
    
    // 獲取該看板下的所有清單
    $lists = $listModel->where('board_id', $boardId)->findAll();
    
    // 為每個清單獲取卡片
    foreach ($lists as &$list) {
      $cards = $cardModel->where('list_id', $list['id'])->findAll();
      
      // 為卡片添加清單標題 (用於前端顯示)
      foreach ($cards as &$card) {
        $card['listTitle'] = $list['title'];
      }
      
      $list['cards'] = $cards;
      
      // 重命名 board_id 為 boardId（前端使用駝峰式命名）
      $list['boardId'] = $list['board_id'];
      $list['order'] = $list['position'];
    }
    
    // 如果沒有找到清單，創建預設清單
    if (empty($lists)) {
      // 建立預設清單
      $defaultLists = [
        ['board_id' => $boardId, 'title' => '待辦', 'position' => 1],
        ['board_id' => $boardId, 'title' => '進行中', 'position' => 2],
        ['board_id' => $boardId, 'title' => '已完成', 'position' => 3]
      ];
      
      foreach ($defaultLists as $listData) {
        $listModel->insert($listData);
        $listId = $listModel->getInsertID();
        
        $newList = [
          'id' => $listId,
          'boardId' => (int)$boardId,
          'board_id' => (int)$boardId,
          'title' => $listData['title'],
          'order' => $listData['position'],
          'position' => $listData['position'],
          'cards' => []
        ];
        
        $lists[] = $newList;
      }
    }

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

  /**
   * 重設看板（清除所有清單和卡片，重新設置預設清單）
   * POST /boards/{boardId}/reset
   * @param int $boardId
   * @return \CodeIgniter\HTTP\Response
   */
  public function resetBoard($boardId)
  {
    $this->setCorsHeaders();
    
    // 建立模型實例
    $listModel = new ListModel();
    $cardModel = new CardModel();
    
    try {
      // 先刪除該看板下所有清單相關的卡片
      $lists = $listModel->where('board_id', $boardId)->findAll();
      foreach ($lists as $list) {
        $cardModel->where('list_id', $list['id'])->delete();
      }
      
      // 再刪除所有清單
      $listModel->where('board_id', $boardId)->delete();
      
      // 建立預設清單
      $defaultLists = [
        ['board_id' => $boardId, 'title' => '待辦', 'position' => 1],
        ['board_id' => $boardId, 'title' => '進行中', 'position' => 2],
        ['board_id' => $boardId, 'title' => '已完成', 'position' => 3]
      ];
      
      foreach ($defaultLists as $listData) {
        $listModel->insert($listData);
      }
      
      // 獲取新建立的清單
      $newLists = $listModel->where('board_id', $boardId)->findAll();
      
      // 為新的清單添加空的卡片陣列
      foreach ($newLists as &$list) {
        $list['cards'] = [];
      }
      
      return $this->respond([
        'status' => 'success',
        'message' => '看板已重設',
        'data' => $newLists
      ]);
      
    } catch (\Exception $e) {
      return $this->respond([
        'status' => 'error',
        'message' => '重設看板失敗: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * 刪除清單
   * DELETE /lists/{listId}
   * 
   * @param int $listId
   * @return \CodeIgniter\HTTP\Response
   */
  public function deleteList($listId)
  {
    $this->setCorsHeaders();
    
    // 建立模型實例
    $listModel = new ListModel();
    $cardModel = new CardModel();
    
    // 檢查清單是否存在
    $list = $listModel->find($listId);
    if (!$list) {
      return $this->respond([
        'status' => 'error',
        'message' => "清單不存在 (ID: {$listId})"
      ], 404);
    }
    
    try {
      // 先刪除該清單下的所有卡片
      $cardModel->where('list_id', $listId)->delete();
      
      // 再刪除清單本身
      $listModel->delete($listId);
      
      return $this->respond([
        'status' => 'success',
        'message' => '清單已成功刪除',
        'data' => [
          'id' => $listId
        ]
      ]);
    } catch (\Exception $e) {
      return $this->respond([
        'status' => 'error',
        'message' => '刪除清單失敗: ' . $e->getMessage()
      ], 500);
    }
  }
}
