<?php

namespace Controllers\Api;

use Controllers\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
class AbstractApiController extends AbstractController {

    /**
     * Respond with JSON
     * @param  array $data
     * @return
     */
    public function sendJson($data)
    {
        $response = new JsonResponse($data);
        $response->send();
        die();
    }

    public function sendError(int $code): void
    {
        http_response_code($code);
        $this->sendJson(['status' => false, 'error' => JsonResponse::$statusTexts[$code]]);
    }

    protected function render(string $template, ?array $parameters = [])
    {
        return $this->twig->render($template, $parameters);
    }
}