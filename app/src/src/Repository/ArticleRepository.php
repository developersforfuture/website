<?php

namespace App\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use Doctrine\ODM\PHPCR\Query\Query;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class ArticleRepository
{
    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    public function __construct(DocumentManagerInterface $documentManager)
    {
        $this->documentManager = $documentManager;
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

        return $result instanceof ArrayCollection ? $result->toArray() : [];
    }

}
