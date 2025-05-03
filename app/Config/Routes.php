<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// Auth routes
$routes->post('auth/login', ['to' => 'Auth::login']);
$routes->options('auth/login', ['to' => 'Auth::options']);

// API routes
$routes->get('api', ['to' => 'Api::index']);
$routes->get('api/boards', ['to' => 'Api::boards']);
$routes->get('boards/(:num)/lists', ['to' => 'Api::listsByBoard/$1']);
$routes->post('boards/(:num)/lists', ['to' => 'Api::createList/$1']);
$routes->delete('lists/(:num)', ['to' => 'Api::deleteList/$1']);

$routes->get('lists/(:num)/cards', ['to' => 'Api::cardsByList/$1']);
$routes->post('lists/(:num)/cards', ['to' => 'Api::createCard/$1']);
$routes->put('cards/(:num)', ['to' => 'Api::updateCard/$1']);
$routes->delete('cards/(:num)', ['to' => 'Api::deleteCard/$1']);

// 看板相關路由
$routes->group('boards', function ($routes) {
    $routes->post('(:num)/reset', ['to' => 'Boards::reset/$1']);
});

// 全域處理 OPTIONS 請求，讓 CORS filter 正常運作
$routes->options('(:any)', ['to' => 'Api::options']);

// 其他路由設定
$routes->cli('(:any)', ['to' => 'CommandApi::index/$1']);
