<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class Boards extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\BoardModel';
    protected $format = 'json';

    /**
     * 重設看板為預設狀態
     * 
     * @param int $id 看板 ID
     */
    public function reset($id = null)
    {
        try {
            if ($id === null) {
                return $this->failValidationError('看板 ID 不能為空');
            }

            $result = $this->model->reset($id);
            
            if ($result === false) {
                return $this->failServerError('重設看板失敗');
            }

            return $this->respondUpdated(['message' => '看板已重設為預設狀態']);
        } catch (\Exception $e) {
            log_message('error', '[BoardsController] 重設看板時發生錯誤: ' . $e->getMessage());
            return $this->failServerError('重設看板時發生錯誤');
        }
    }
}
