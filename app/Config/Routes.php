<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Home::dashboard');
$routes->get('/dashboard', 'Home::dashboard');

$routes->get('/auth/login','AuthController::login');
$routes->get('/auth/logout','AuthController::logout');

$routes->group('/emp', function (RouteCollection $routes) {
    $routes->get('/','EmployeeController::getEmployees');
    $routes->post('/','EmployeeController::addEmployee');
    $routes->put('/','EmployeeController::updateEmployee');
    $routes->delete('/','EmployeeController::deleteEmployee');
});