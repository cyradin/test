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
    }
}