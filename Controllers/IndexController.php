<?php

namespace Controllers;

class IndexController extends Controller
{
    public function indexAction()
    {
        return $this->render('index.html.twig', [
            'title' => 'Main page'
        ]);
    }
}