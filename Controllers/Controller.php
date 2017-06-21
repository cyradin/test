<?php

namespace Controllers;

/**
 * Base controller class
 */
abstract class Controller
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

    protected function render(string $template, ?array $parameters = []) : void
    {
        echo $this->twig->render($template, $parameters);
    }
}