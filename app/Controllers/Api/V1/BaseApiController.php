<?php
namespace App\Controllers\Api\V1;

use CodeIgniter\RESTful\ResourceController;

class BaseApiController extends ResourceController
{
    protected function success($data = null, $message = 'Success', $code = 200)
    {
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null
        ])->setStatusCode($code);
    }
    
    protected function error($message = 'Error', $errors = null, $code = 400)
    {
        return $this->response->setJSON([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors
        ])->setStatusCode($code);
    }
}
