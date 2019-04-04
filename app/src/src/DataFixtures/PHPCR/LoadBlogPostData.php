<?php

namespace App\DataFixtures\PHPCR;

use App\Controller\BlogListController;
use App\Document\StaticPage;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use PHPCR\Util\NodeHelper;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class LoadBlogPostData implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

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

        $session = $manager->getPhpcrSession();
        $contentBasePath = $this->container->getParameter('cmf_content.persistence.phpcr.content_basepath');
        $routesBasePath = $this->container->getParameter('cmf_content.persistence.phpcr.routes_basepath');
        $blogBasePath = $contentBasePath . '/blog';
        NodeHelper::createPath($session, $blogBasePath);
        $blogParentNode = $manager->find(null, $blogBasePath);

        $blogRoutesBasePaht = $routesBasePath.'/blog';
        $blogParentRoute = new Route();
        $blogParentRoute->setName('blog');
        $blogParentRoute->setDefault('controller', BlogListController::class.'::index');
        $manager->persist($blogParentRoute);

        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../Resources/data/blog/');
        // check if there are any search results
        if (!$finder->hasResults()) {
            return;
        }

        foreach ($finder as $file) {
            $fileNameWithExtension = $file->getRelativePathname();

            $yaml = new Parser();
            $data = $yaml->parse(file_get_contents($fileNameWithExtension));

            $this->loadBlogData($manager, $data, $blogParentNode);
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 30;
    }

    private function loadBlogData(DocumentManager $manager, array $data, $blogParentNode)
    {
        $blogPage = new StaticPage();
        $blogPage->setParentDocument($blogParentNode);
        $blogPage->setTitle($data['title']);
        $blogPage->setBody($data['body']);
    }
}
