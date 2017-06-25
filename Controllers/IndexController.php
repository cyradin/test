<?php

namespace Controllers;

use Models\Link;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    const ITEMS_ON_PAGE = 10;

    public function indexAction(Request $request)
    {
        return $this->render('index.html.twig', [
            'title' => 'Main page'
        ]);
    }

    public function urlListAction(Request $request)
    {
        $page = $request->query->get('page') ?? 1;

        $count = Link::count([]);
        $pageCount = ceil($count / static::ITEMS_ON_PAGE);

        if ($page > $pageCount) {
            $this->sendError(404);
        }

        $links = Link::find([], ['id' => 'DESC'], [($page-1) * static::ITEMS_ON_PAGE, static::ITEMS_ON_PAGE]);

        return $this->render('links.html.twig', [
            'title' => 'Link list',
            'links' => $links,
            'currentUrl' => $request->getBaseUrl() . $request->getPathInfo(),
            'currentPage' => $page,
            'pageCount' => $pageCount
        ]);
    }
}
