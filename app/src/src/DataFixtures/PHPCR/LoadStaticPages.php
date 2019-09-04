<?php

namespace App\DataFixtures\PHPCR;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use PHPCR\Util\NodeHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Parser;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class LoadStaticPages extends AbstractPageLoader
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
        $this->manager = $manager;

        if ($this->container === null) {
            throw new \Exception('Container cannot be null.');
        }

        $session = $this->manager->getPhpcrSession();

        $basepath = $this->container->getParameter('cmf_content.persistence.phpcr.content_basepath');
        NodeHelper::createPath($session, $basepath);

        $yaml = new Parser();
        $configFilePath = __DIR__ . '/../../Resources/data/static_pages.yml';
        $content = file_get_contents($configFilePath);
        if ($content === false) {
            throw new \Exception("'$configFilePath' could not be read.");
        }
        $data = $yaml->parse($content);

        $parent = $this->manager->find(null, $basepath);
        foreach ($data['static'] as $overview) {
            $page = $this->loadStaticPageData($overview, $basepath, $parent);

            if (isset($overview['blocks']) && is_array($overview['blocks'])) {
                foreach ($overview['blocks'] as $name => $block) {
                    $this->loadBlock($page, $name, $block);
                }
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
        return 20;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
