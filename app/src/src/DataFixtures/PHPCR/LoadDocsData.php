<?php

namespace App\DataFixtures\PHPCR;

use App\Controller\DocsListController;
use App\Document\Document;
use App\Services\Utils;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use PHPCR\Util\NodeHelper;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Parser;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class LoadDocsData implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var ObjectManager|DocumentManager
     */
    private $manager;

    /**
     * Sets the container.
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager|DocumentManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (!$manager instanceof DocumentManager) {
            $class = get_class($manager);

            throw new \RuntimeException("Fixture requires a PHPCR ODM DocumentManager instance, instance of '$class' given.");
        }

        $this->manager = $manager;


        $session = $this->manager->getPhpcrSession();
        $contentBasePath = $this->container->getParameter('cmf_content.persistence.phpcr.content_basepath');
        $routesBasePaths = $this->container->getParameter('cmf_routing.dynamic.persistence.phpcr.route_basepaths');
        $routesBasePath = array_shift($routesBasePaths);
        $docsBasePath = $contentBasePath . '/blog';
        NodeHelper::createPath($session, $docsBasePath);
        $docsParentNode = $this->manager->find(null, $docsBasePath);
        $docsParentRouteNode = $this->manager->find(null, $routesBasePath);

        $yaml = new Parser();
        $data = $yaml->parse(file_get_contents(__DIR__ . '/../../Resources/data/docs.yml'));

        $docsParentRoute = new Route();
        $docsParentRoute->setName('docs');
        $docsParentRoute->setParentDocument($docsParentRouteNode);
        $docsParentRoute->setDefault('_controller', DocsListController::class.'::indexAction');
        $this->manager->persist($docsParentRoute);

        foreach ($data['docs'] as $docData) {
            $document = new Document();
            $document->setParentDocument($docsParentNode);
            $document->setName(Utils::createSeoUrl($docData['title']));
            $document->setTitle($docData['title']);
            $document->setLink($docData['title']);
            $document->setDescription($docData['title']);
            $document->setCategories($docData['categories']);
            $document->setBody("");
            $manager->persist($document);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 20;
    }
}
