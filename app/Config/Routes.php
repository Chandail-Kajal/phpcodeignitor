<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::login');
$routes->get('/login', 'Home::login');

$routes->get('/add-emp', 'Home::index', ['filter' => 'auth']);
$routes->get('/dashboard', 'Home::dashboard', ['filter' => 'auth']);

$routes->group('/api', function (RouteCollection $routes) {
    $routes->group('auth', function (RouteCollection $routes) {
        $routes->post('login', 'AuthController::login');
        $routes->get('logout', 'AuthController::logout');


    });
    $routes->group('emp', ['filter' => 'auth'], function (RouteCollection $routes) {
        $routes->get('', 'EmployeeController::getEmployees');
        $routes->post('', 'EmployeeController::addEmployee');
        $routes->put('', 'EmployeeController::updateEmployee');
        $routes->delete('', 'EmployeeController::deleteEmployee');
    });
});
