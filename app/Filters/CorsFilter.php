<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * CORS 過濾器
 * 
 * 適用於 Vue.js 前端和 CodeIgniter 後端間的跨域請求
 * 特別設計用於處理使用憑證的請求
 */
class CorsFilter implements FilterInterface
{
    /**
     * 在頁面執行前進行前置處理
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 取得來源網址
        $origin = $request->getHeaderLine('Origin');
        
        // 如果 Origin 不存在，設置為允許所有來源
        if (empty($origin)) {
            $origin = '*';
        }
        
        // 預設值
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Origin, X-API-KEY, X-Requested-With, Content-Type, Accept, Authorization, Access-Control-Request-Method, Access-Control-Allow-Headers');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        
        // OPTIONS 請求處理
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == 'OPTIONS') {
            header('Access-Control-Max-Age: 86400'); // 快取 24 小時
            exit(0);
        }
    }

    /**
     * 在頁面執行後進行後置處理
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 取得來源網址
        $origin = $request->getHeaderLine('Origin');
        
        // 如果 Origin 不存在，設置為允許所有來源
        if (empty($origin)) {
            $origin = '*';
        }
        
        // 設定 CORS 標頭
        $response->setHeader('Access-Control-Allow-Origin', $origin);
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, X-API-KEY, X-Requested-With, Content-Type, Accept, Authorization, Access-Control-Request-Method');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
        
        return $response;
    }
}
