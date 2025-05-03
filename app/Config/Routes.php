<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('api', 'Api::index');
$routes->get('boards/(:num)/lists', 'Api::listsByBoard/$1');
$routes->post('boards/(:num)/lists', 'Api::createList/$1');
$routes->get('lists/(:num)/cards', 'Api::cardsByList/$1');
$routes->post('lists/(:num)/cards', 'Api::createCard/$1');
$routes->put('cards/(:num)', 'Api::updateCard/$1');
$routes->delete('cards/(:num)', 'Api::deleteCard/$1');

// 全域處理 OPTIONS 請求，讓 CORS filter 正常運作
$routes->options('(:any)', 'Home::options');
