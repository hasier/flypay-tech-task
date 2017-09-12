<?php

class AuthException extends Exception {
}

class ValidationException extends Exception {
}

class ExceptionHandler {
   public function __invoke($request, $response, $exception) {
        if ($exception instanceof AuthException) {
          return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('errorKey' => 'auth_error', 'message' => $exception->getMessage())));
        } else if ($exception instanceof ValidationException) {
          return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('errorKey' => 'validation_error', 'message' => $exception->getMessage())));
        }
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('errorKey' => 'server_error', 'message' => 'Oops, something went wrong...' . $exception->getTraceAsString())));
   }
}
