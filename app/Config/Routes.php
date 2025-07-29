<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::login');
$routes->get('/login', 'Home::login');
$routes->get('/add-emp','Home::index');

$routes->post('/auth/login','AuthController::login');
$routes->get('/auth/logout','AuthController::logout');

$routes->get('/dashboard', 'Home::dashboard', ['filter' => 'auth']);

$routes->group('/emp', ['filter' => 'auth'], function (RouteCollection $routes) {
    $routes->get('/', 'EmployeeController::getEmployees');
    $routes->post('/', 'EmployeeController::addEmployee');
    $routes->put('/', 'EmployeeController::updateEmployee');
    $routes->delete('/', 'EmployeeController::deleteEmployee');
});
