<?php

namespace aktivgo\chat\app;

class HttpResponse
{
    // Отправляет http ответ(код и информацию)
    public static function toSendResponse(array $message, int $code)
    {
        http_response_code($code);
        echo json_encode($message);
    }
}