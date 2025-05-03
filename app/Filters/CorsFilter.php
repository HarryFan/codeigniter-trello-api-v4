<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 取得請求的來源
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
        
        // 允許常用的前端開發來源
        $allowedOrigins = [
            'http://127.0.0.1:5173',
            'http://127.0.0.1:5174',
            'http://127.0.0.1:5175',
            'http://localhost:5173',
            'http://localhost:5174',
            'http://localhost:5175'
        ];
        
        // 如果請求來源在允許清單中，則設置對應的標頭
        if (in_array($origin, $allowedOrigins) || $origin === '*') {
            header('Access-Control-Allow-Origin: ' . $origin);
        } else {
            // 如果不在清單中，預設允許所有來源（開發環境）
            header('Access-Control-Allow-Origin: *');
        }
        
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, X-User-Id');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
        header('Access-Control-Max-Age: 86400');  // 24小時
        
        // 處理 OPTIONS 預檢請求
        if ($request->getMethod() === 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 取得請求的來源
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
        
        // 允許常用的前端開發來源
        $allowedOrigins = [
            'http://127.0.0.1:5173',
            'http://127.0.0.1:5174',
            'http://127.0.0.1:5175',
            'http://localhost:5173',
            'http://localhost:5174',
            'http://localhost:5175'
        ];
        
        // 如果請求來源在允許清單中，則設置對應的標頭
        if (in_array($origin, $allowedOrigins) || $origin === '*') {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
        } else {
            // 如果不在清單中，預設允許所有來源（開發環境）
            $response->setHeader('Access-Control-Allow-Origin', '*');
        }
        
        $response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization, X-User-Id')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
                ->setHeader('Access-Control-Allow-Credentials', 'true')
                ->setHeader('Access-Control-Max-Age', '86400');  // 24小時

        return $response;
    }
}
