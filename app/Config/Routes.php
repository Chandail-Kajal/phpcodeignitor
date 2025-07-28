<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login','AuthController::login');
$routes->get('/logout','AuthController::logout');
$routes->group('/emp', function (RouteCollection $routes) {
    $routes->get('/','EmployeeController::getEmployees');
    $routes->post('/','EmployeeController::addEmployee');
    $routes->put('/','EmployeeController::updateEmployee');
    $routes->delete('/','EmployeeController::deleteEmployee');
});