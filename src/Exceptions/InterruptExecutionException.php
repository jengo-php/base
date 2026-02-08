<?php

namespace Jengo\Base\Exceptions;

use CodeIgniter\HTTP\ResponsableInterface;
use CodeIgniter\HTTP\ResponseInterface;

class InterruptExecutionException extends \RuntimeException implements ResponsableInterface
{
    protected ResponseInterface $response;

    public function __construct(
        ResponseInterface $response,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous =
        null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}