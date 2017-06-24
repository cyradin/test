<?php

namespace Controllers;

use Lib\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base controller class
 */
abstract class AbstractController
{
    /**
     * Twig instance
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    protected function render(string $template, ?array $parameters = []): void
    {
        $parameters['validationMessages'] = json_encode(Validator::instance()->getMessages());
        $this->send(
            $this->twig->render($template, $parameters)
        );
    }

    public function send($data)
    {
        $response = new Response($data);
        $response->send();
    }

    public function sendError(int $code): void
    {
        http_response_code($code);
        $this->send($this->twig->render('error.html.twig', [
            'code' => $code,
            'text' => Response::$statusTexts[$code]
        ]));
    }
}