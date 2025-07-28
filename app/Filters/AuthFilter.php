<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (! $session->get('logged_in')) {
          
            if ($request->isAJAX() || $request->hasHeader('Accept') && $request->getHeaderLine('Accept') === 'application/json') {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['message' => 'Unauthorized access']);
            }

          
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
       
    }
}
