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
$routes->post('auth/login', 'Auth::login');
$routes->options('auth/login', 'Auth::options');

// API routes
$routes->get('api', 'Api::index');
$routes->get('api/boards', 'Api::boards');
$routes->get('boards/(:num)/lists', 'Api::listsByBoard/$1');
$routes->post('boards/(:num)/lists', 'Api::createList/$1');
$routes->delete('lists/(:num)', 'Api::deleteList/$1');

$routes->get('lists/(:num)/cards', 'Api::cardsByList/$1');
$routes->post('lists/(:num)/cards', 'Api::createCard/$1');
$routes->put('cards/(:num)', 'Api::updateCard/$1');
$routes->delete('cards/(:num)', 'Api::deleteCard/$1');

// 看板相關路由
$routes->group('boards', function ($routes) {
    $routes->post('(:num)/reset', 'Boards::reset/$1');
});

// 全域處理 OPTIONS 請求，讓 CORS filter 正常運作
$routes->options('(:any)', 'Api::options');

// 其餘路徑維持 Home::options 作為 fallback
$routes->options('(:any)', 'Home::options');
