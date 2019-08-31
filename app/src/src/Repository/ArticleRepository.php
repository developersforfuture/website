<?php

namespace App\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Query\Query;
use Symfony\Cmf\Bundle\CoreBundle\PublishWorkflow\PublishWorkflowChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class ArticleRepository
{
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;
    /**
     * @var PublishWorkflowChecker|AuthorizationCheckerInterface
     */
    private $publishWorkflowChecker;

    public function __construct(DocumentManagerInterface $documentManager, AuthorizationCheckerInterface $publishWorkflowChecker)
    {
        $this->documentManager = $documentManager;
        $this->publishWorkflowChecker = $publishWorkflowChecker;
    }
    public function getAll()
    {
        $this->documentManager->getUnitOfWork()->setFetchDepth(3);
        $queryBuilder = $this->documentManager->createQueryBuilder();

        $queryBuilder
            ->fromDocument('App\Document\BlogPage','c');
        $queryBuilder->orderBy()
            ->asc()->field('c.createdAt')->end()
            ->end();
        $result = $queryBuilder->getQuery()->execute(null, Query::HYDRATE_DOCUMENT);
        $foundArticles = $result instanceof ArrayCollection ? $result->toArray() : [];
        $publishedArticles = [];
        foreach ($foundArticles as $article) {
            if ($this->publishWorkflowChecker->isGranted(PublishWorkflowChecker::VIEW_ANONYMOUS_ATTRIBUTE, $article)) {
                $publishedArticles[] = $article;
            }
        }

        return $publishedArticles;
    }

}
