<?php

namespace App\Controller;

use App\Repository\DocsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class DocsListController extends AbstractController
{
    /**
     * @var DocsRepository
     */
    private $repository;

    public function __construct(DocsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function indexAction()
    {
        return $this->render('list/docs_list.html.twig', ['docs' => $this->repository->getAll()]);
    }
}
