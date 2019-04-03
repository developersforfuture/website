<?php

namespace App\DataFixtures\PHPCR;

use App\Document\IconBlock;
use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use App\Document\StaticPage;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use PHPCR\Util\NodeHelper;
use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\RedirectRoute;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoMetadata;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Parser;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class LoadStaticPages implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager|DocumentManager $manager
     *
     * @throws \Doctrine\ODM\PHPCR\PHPCRException
     */
    public function load(ObjectManager $manager)
    {
        if (!$manager instanceof DocumentManager) {
            $class = get_class($manager);

            throw new \RuntimeException("Fixture requires a PHPCR ODM DocumentManager instance, instance of '$class' given.");
        }

        $session = $manager->getPhpcrSession();

        $basepath = $this->container->getParameter('cmf_content.persistence.phpcr.content_basepath');
        NodeHelper::createPath($session, $basepath);

        $yaml = new Parser();
        $data = $yaml->parse(file_get_contents(__DIR__ . '/../../Resources/data/page.yml'));

        $parent = $manager->find(null, $basepath);
        foreach ($data['static'] as $overview) {
            $path = $basepath.'/'.$overview['name'];
            $page = $manager->find(null, $path);
            if (!$page) {
                $page = new StaticPage();
                $page->setName($overview['name']);
                $page->setSeoMetadata(new SeoMetadata());
                $page->setParentDocument($parent);
                $manager->persist($page);
            }

            if (is_array($overview['title'])) {
                foreach ($overview['title'] as $locale => $title) {
                    $page->setTitle($title);
                    $page->setBody($overview['body'][$locale]);
                    $page->setDescription($overview['description'][$locale]);

                    $manager->bindTranslation($page, $locale);
                }
            } else {
                $page->setTitle($overview['title']);
                $page->setBody($overview['body']);
            }

            if (isset($overview['publishable']) && false === $overview['publishable']) {
                $page->setPublishable(false);
            }

            if (!empty($overview['publishStartDate'])) {
                try {
                    $page->setPublishStartDate(new DateTime($overview['publishStartDate']));
                } catch (\Exception $e) {

                }
            }

            if (!empty($overview['publishEndDate'])) {
                try {
                    $page->setPublishEndDate(new DateTime($overview['publishEndDate']));
                } catch (\Exception $e) {

                }
            }
        }

        if (isset($overview['blocks'])) {
            foreach ($overview['blocks'] as $name => $block) {
                $this->loadBlock($manager, $page, $name, $block);
            }
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

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load a block from the fixtures and create / update the node. Recurse if there are children.
     *
     * @param ObjectManager|DocumentManagerInterface $manager the document manager
     * @param object $parent
     * @param string $name the name of the block
     * @param array $block the block definition
     *
     * @throws \Exception
     */
    private function loadBlock(ObjectManager $manager, $parent, $name, $block)
    {
        $className = $block['class'];
        $document = $manager->find(null, $this->getIdentifier($manager, $parent).'/'.$name);
        $class = $manager->getClassMetadata($className);
        if ($document && get_class($document) != $className) {
            $manager->remove($document);
            $document = null;
        }
        if (!$document) {
            $document = $class->newInstance();

            // $document needs to be an instance of BaseBlock ...
            $document->setParentDocument($parent);
            $document->setName($name);
        }

        if ('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ReferenceBlock' == $className) {
            $referencedBlock = $manager->find(null, $block['referencedBlock']);
            if (null === $referencedBlock) {
                throw new \Exception('did not find '.$block['referencedBlock']);
            }
            $document->setReferencedBlock($referencedBlock);
        } elseif ('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock' == $className) {
            $document->setActionName($block['actionName']);
        }

        $manager->persist($document);

        // set properties
        if (isset($block['properties'])) {
            foreach ($block['properties'] as $propName => $prop) {
                if (is_array($prop)) {
                    foreach ($prop as $lang => $translatedProp) {
                        $class->reflFields[$propName]->setValue($document, $translatedProp);
                    }
                    $manager->bindTranslation($document, $lang);
                } else {
                    $class->reflFields[$propName]->setValue($document, $prop);
                }

            }
        }
        // create children
        if (isset($block['children'])) {
            foreach ($block['children'] as $childName => $child) {
                $this->loadBlock($manager, $document, $childName, $child);
            }
        }
    }

    private function getIdentifier(DocumentManagerInterface $manager, $document)
    {
        $class = $manager->getClassMetadata(get_class($document));

        return $class->getIdentifierValue($document);
    }
}
