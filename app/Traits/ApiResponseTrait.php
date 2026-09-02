<?php

namespace App\Traits;

trait ApiResponseTrait
{
    /**
     * Standard success JSON response.
     *
     * @param mixed  $data
     * @param string $message
     * @param int    $statusCode
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    protected function successResponse($data = [], string $message = 'Success', int $statusCode = 200)
    {
        return $this->response->setStatusCode($statusCode)->setJSON([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data
        ]);
    }

    /**
     * Standard error JSON response.
     *
     * @param string $message
     * @param int    $statusCode
     * @param mixed  $errors
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = [])
    {
        return $this->response->setStatusCode($statusCode)->setJSON([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors
        ]);
    }
}
