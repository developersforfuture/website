<?php

namespace App\DataFixtures\Document;

use App\DataFixtures\ORM\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use RuntimeException;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Bundle\PageBundle\Document\BasePageDocument;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Bundle\SnippetBundle\Document\SnippetDocument;
use Sulu\Bundle\SnippetBundle\Snippet\DefaultSnippetManagerInterface;
use Sulu\Component\Content\Document\RedirectType;
use Sulu\Component\Content\Document\WorkflowStage;
use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Component\DocumentManager\Exception\DocumentManagerException;
use Sulu\Component\DocumentManager\Exception\MetadataNotFoundException;
use Sulu\Component\PHPCR\PathCleanup;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class DocumentFixture implements DocumentFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    /**
     * @var PathCleanup
     */
    protected $pathCleanup;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /** @var DefaultSnippetManagerInterface */
    protected $defaultSnippetManager;

    /**
     * @throws Exception
     */
    protected function loadContactSnippet(DocumentManager $documentManager): SnippetDocument
    {
        $data = [
            'locale' => AppFixtures::LOCALE_EN,
            'title' => 'Z',
            'contact' => [
                'id' => 1,
            ],
        ];

        $snippetDocument = $this->createSnippet($documentManager, 'contact', $data);

        $this->getDefaultSnippetManager()->save('demo', 'contact', $snippetDocument->getUuid(), AppFixtures::LOCALE_EN);

        return $snippetDocument;
    }

    /**
     * @param mixed[] $data
     *
     * @throws MetadataNotFoundException
     */
    protected function createSnippet(DocumentManager $documentManager, string $structureType, array $data): SnippetDocument
    {
        $locale = isset($data['locale']) && $data['locale'] ? $data['locale'] : AppFixtures::LOCALE_EN;

        /** @var SnippetDocument $snippetDocument */
        $snippetDocument = null;

        try {
            if (!isset($data['id']) || !$data['id']) {
                throw new Exception();
            }

            $snippetDocument = $documentManager->find($data['id'], $locale);
        } catch (Exception $e) {
            $snippetDocument = $documentManager->create('snippet');
        }

        $snippetDocument->getUuid();
        $snippetDocument->setLocale($locale);
        $snippetDocument->setTitle($data['title']);
        $snippetDocument->setStructureType($structureType);
        $snippetDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $snippetDocument->getStructure()->bind($data);

        $documentManager->persist($snippetDocument, $locale, ['parent_path' => '/cmf/snippets']);
        $documentManager->publish($snippetDocument, $locale);

        return $snippetDocument;
    }

    /**
     * @throws DocumentManagerException
     */
    protected function updatePages(DocumentManager $documentManager, string $locale): void
    {
        /** @var BasePageDocument $artistsDocument */
        $artistsDocument = $documentManager->find('/cmf/econ4future/contents/artists', $locale);

        $data = $artistsDocument->getStructure()->toArray();

        $data['elements'] = [
            'sortBy' => 'published',
            'sortMethod' => 'asc',
            'dataSource' => $artistsDocument->getUuid(),
        ];

        $artistsDocument->getStructure()->bind($data);

        $documentManager->persist($artistsDocument, $locale);
        $documentManager->publish($artistsDocument, $locale);
    }

    /**
     * @param mixed[] $data
     *
     * @throws MetadataNotFoundException
     */
    protected function createPage(DocumentManager $documentManager, array $data): BasePageDocument
    {
        $locale = isset($data['locale']) && $data['locale'] ? $data['locale'] : AppFixtures::LOCALE_EN;

        if (!isset($data['url'])) {
            $url = $this->getPathCleanup()->cleanup('/' . $data['title']);
            if (isset($data['parent_path'])) {
                $url = mb_substr($data['parent_path'], mb_strlen('/cmf/demo/contents')) . $url;
            }

            $data['url'] = $url;
        }

        $extensionData = [
            'seo' => $data['seo'] ?? [],
            'excerpt' => $data['excerpt'] ?? [],
        ];

        unset($data['excerpt']);
        unset($data['seo']);

        /** @var PageDocument $pageDocument */
        $pageDocument = null;

        try {
            if (!isset($data['id']) || !$data['id']) {
                throw new Exception();
            }

            $pageDocument = $documentManager->find($data['id'], $locale);
        } catch (Exception $e) {
            $pageDocument = $documentManager->create('page');
        }

        $pageDocument->setNavigationContexts($data['navigationContexts'] ?? []);
        $pageDocument->setLocale($locale);
        $pageDocument->setTitle($data['title']);
        $pageDocument->setResourceSegment($data['url']);
        $pageDocument->setStructureType($data['structureType'] ?? 'default');
        $pageDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $pageDocument->getStructure()->bind($data);
        $pageDocument->setAuthor(1);
        $pageDocument->setExtensionsData($extensionData);

        if (isset($data['redirect'])) {
            $pageDocument->setRedirectType(RedirectType::EXTERNAL);
            $pageDocument->setRedirectExternal($data['redirect']);
        }

        $documentManager->persist(
            $pageDocument,
            $locale,
            ['parent_path' => $data['parent_path'] ?? '/cmf/demo/contents']
        );

        // Set dataSource to current page after persist as uuid is before not available
        if (isset($data['pages']['dataSource']) && '__CURRENT__' === $data['pages']['dataSource']) {
            $pageDocument->getStructure()->bind(
                [
                    'pages' => array_merge(
                        $data['pages'],
                        [
                            'dataSource' => $pageDocument->getUuid(),
                        ]
                    ),
                ]
            );

            $documentManager->persist(
                $pageDocument,
                $locale,
                ['parent_path' => $data['parent_path'] ?? '/cmf/demo/contents']
            );
        }

        $documentManager->publish($pageDocument, $locale);

        return $pageDocument;
    }

    protected function getPathCleanup(): PathCleanup
    {
        if (null === $this->pathCleanup) {
            $this->pathCleanup = $this->container->get('sulu.content.path_cleaner');
        }

        return $this->pathCleanup;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        }

        return $this->entityManager;
    }

    protected function getDefaultSnippetManager(): DefaultSnippetManagerInterface
    {
        if (null === $this->defaultSnippetManager) {
            $this->defaultSnippetManager = $this->container->get('sulu_snippet.default_snippet.manager');
        }

        return $this->defaultSnippetManager;
    }

    protected function getMediaId(string $name): int
    {
        try {
            $id = $this->getEntityManager()->createQueryBuilder()
                ->from(Media::class, 'media')
                ->select('media.id')
                ->innerJoin('media.files', 'file')
                ->innerJoin('file.fileVersions', 'fileVersion')
                ->where('fileVersion.name = :name')
                ->setMaxResults(1)
                ->setParameter('name', $name)
                ->getQuery()->getSingleScalarResult();

            return (int) $id;
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException(sprintf('Too many images with the name "%s" found.', $name), 0, $e);
        }
    }
}
