<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('api', 'Api::index');
$routes->get('boards/(:num)/lists', 'Api::listsByBoard/$1');
$routes->post('boards/(:num)/lists', 'Api::createList/$1');
$routes->post('boards/(:num)/reset', 'Api::resetBoard/$1');
$routes->get('lists/(:num)/cards', 'Api::cardsByList/$1');
$routes->post('lists/(:num)/cards', 'Api::createCard/$1');
$routes->delete('lists/(:num)', 'Api::deleteList/$1');
$routes->put('cards/(:num)', 'Api::updateCard/$1');
$routes->delete('cards/(:num)', 'Api::deleteCard/$1');

// 認證路由
$routes->post('auth/login', 'Auth::login');
$routes->post('auth/register', 'Auth::register');
$routes->post('auth/logout', 'Auth::logout');
$routes->options('auth/login', 'Auth::options');
$routes->options('auth/register', 'Auth::options');
$routes->options('auth/logout', 'Auth::options');

// 全域處理 OPTIONS 請求，讓 CORS filter 正常運作
$routes->options('api', 'Api::options');
$routes->options('api/boards', 'Api::options');
$routes->options('boards/(:num)/lists', 'Api::options_wildcard');
$routes->options('boards/(:num)/reset', 'Api::options_wildcard');
$routes->options('lists/(:num)/cards', 'Api::options_wildcard');
$routes->options('cards/(:num)', 'Api::options_wildcard');

// 其餘路徑維持 Home::options 作為 fallback
$routes->options('(:any)', 'Home::options');

$routes->get('api/boards', 'Api::boards');
