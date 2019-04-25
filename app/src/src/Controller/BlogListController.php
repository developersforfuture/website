<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class BlogListController extends AbstractController
{
    /**
     * @var ArticleRepository
     */
    private $repository;

    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function indexAction()
    {
        return $this->render(   'pages/dynamic_content/article_list.html.twig', ['articles' => $this->repository->getAll()]);
    }
}
