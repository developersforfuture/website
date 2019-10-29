<?php

namespace App\DataFixtures\PHPCR;

use App\Controller\BlogListController;
use App\Document\BlogPage;
use App\Document\Document;
use App\Document\Speaker;
use App\Document\StaticPage;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use PHPCR\NodeInterface;
use PHPCR\Util\NodeHelper;
use Symfony\Cmf\Bundle\RoutingAutoBundle\Tests\Fixtures\App\Document\Blog;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class LoadBlogPostData extends AbstractPageLoader
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager|DocumentManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        if (!$manager instanceof DocumentManager) {
            $class = get_class($manager);

            throw new \RuntimeException("Fixture requires a PHPCR ODM DocumentManager instance, instance of '$class' given.");
        }

        $parentDocument = $manager->find(null, '/cms/content');
        $this->manager = $manager;

        if ($this->container === null) {
            throw new \Exception('Container cannot be null.');
        }

        $session = $this->manager->getPhpcrSession();
        $contentBasePath = $this->container->getParameter('cmf_content.persistence.phpcr.content_basepath');
        $routesBasePaths = $this->container->getParameter('cmf_routing.dynamic.persistence.phpcr.route_basepaths');
        $routesBasePath = array_shift($routesBasePaths);
        $blogBasePath = $contentBasePath . '/blog';
        NodeHelper::createPath($session, $blogBasePath);
        $blogParentNode = $this->manager->find(null, $blogBasePath);
        if ($blogParentNode === null) {
            throw new \Exception('$blogParentNode cannot be null.');
        }

        $blogParentRouteNode = $this->manager->find(null, $routesBasePath);
        if ($blogParentRouteNode === null) {
            throw new \Exception('$blogParentRouteNode cannot be null.');
        }

        $blogParentRoute = new Route();
        $blogParentRoute->setName('blog');
        $blogParentRoute->setParentDocument($blogParentRouteNode);
        $blogParentRoute->setDefault('_controller', BlogListController::class.'::indexAction');
        $this->manager->persist($blogParentRoute);

        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../Resources/data/blog/');
        // check if there are any search results
        if (!$finder->hasResults()) {
            print("found no files for blog data");
            return;
        }

        foreach ($finder as $file) {
            $fileNameWithExtension = $file->getRelativePathname();

            $yaml = new Parser();
            $configFilePath = $file->getPath() . '/' . $fileNameWithExtension;
            $content = file_get_contents($configFilePath);
            if ($content === false) {
                throw new \Exception("'$configFilePath' could not be read.");
            }
            $data = $yaml->parse($content);
            /** @var BlogPage $page */
            $page = $this->loadStaticPageData($data, $contentBasePath, $blogParentNode);

            if (isset($data['blocks'])) {
                foreach ($data['blocks'] as $name => $block) {
                    $this->loadBlock($page, $name, $block);
                }
            }

            if (isset($overview['speaker'])) {
                $this->loadSpeakerData($page, $data['speaker']);
            }
        }

        $this->manager->flush();
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

    private function loadSpeakerData(BlogPage $page, $data): BlogPage
    {
        $speaker = new Speaker();
        $speaker->setSpeakerName(isset($data['name']) ? $data['name'] : null);
        $speaker->setTwitter(isset($data['twitter']) ? $data['twitter'] : null);
        $speaker->setImage(isset($data['image']) ? $data['image'] : null);
        $speaker->setBio(isset($data['bio']) ? $data['bio'] : null);
        $speaker->setParentDocument($page);

        $page->setSpeaker($speaker);

        return $page;
    }
}
