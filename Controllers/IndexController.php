<?php

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    public function indexAction(Request $request)
    {
        return $this->render('index.html.twig', [
            'title' => 'Main page'
        ]);
    }

    public function urlListAction(Request $request)
    {

    }
}
