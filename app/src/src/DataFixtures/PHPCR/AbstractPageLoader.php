<?php

namespace App\DataFixtures\PHPCR;

use App\Document\StaticPage;
use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;
use PHPCR\NodeInterface;
use Symfony\Cmf\Bundle\SeoBundle\Model\SeoMetadata;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
abstract class AbstractPageLoader implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ObjectManager|DocumentManager
     */
    protected $manager;

    /**
     * Sets the container.
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function loadStaticPageData(array $data, string $basePath, $parent)
    {
        $path = $basePath.'/'.$data['name'];
        $page = $this->manager->find(null, $path);
        if (!$page) {
            $page = isset($data['class']) ? new $data['class'] : new StaticPage();
            $page->setName($data['name']);
            $page->setSeoMetadata(new SeoMetadata());
            $page->setParentDocument($parent);
            $this->manager->persist($page);
        }

        if (is_array($data['title'])) {
            foreach ($data['title'] as $locale => $title) {
                $page->setTitle($title);
                $page->setBody($data['body'][$locale]);
                $page->setDescription($data['description'][$locale]);

                try {
                    $this->manager->bindTranslation($page, $locale);
                } catch (\Exception $e) {
                }
            }
        } else {
            $page->setTitle($data['title']);
            $page->setBody($data['body']);
            $page->setDescription($data['description']);
        }

        if (isset($data['publishable']) && false === $data['publishable']) {
            $page->setPublishable(false);
        }

        if (!empty($data['publishStartDate'])) {
            try {
                $page->setPublishStartDate(new DateTime($data['publishStartDate']));
            } catch (\Exception $e) {

            }
        }

        if (!empty($data['publishEndDate'])) {
            try {
                $page->setPublishEndDate(new DateTime($data['publishEndDate']));
            } catch (\Exception $e) {

            }
        }

        return $page;
    }


    /**
     * Load a block from the fixtures and create / update the node. Recurse if there are children.
     *
     * @param ObjectManager|DocumentManagerInterface $this->manager the document manager
     * @param object $parent
     * @param string $name the name of the block
     * @param array $block the block definition
     *
     * @throws \Exception
     */
    protected function loadBlock($parent, $name, $block)
    {
        $className = $block['class'];
        $document = $this->manager->find(null, $this->getIdentifier($parent).'/'.$name);
        $class = $this->manager->getClassMetadata($className);
        if ($document && get_class($document) != $className) {
            $this->manager->remove($document);
            $document = null;
        }
        if (!$document) {
            $document = $class->newInstance();

            // $document needs to be an instance of BaseBlock ...
            $document->setParentDocument($parent);
            $document->setName($name);
        }

        if ('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ReferenceBlock' == $className) {
            $referencedBlock = $this->manager->find(null, $block['referencedBlock']);
            if (null === $referencedBlock) {
                throw new \Exception('did not find '.$block['referencedBlock']);
            }
            $document->setReferencedBlock($referencedBlock);
        } elseif ('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ActionBlock' == $className) {
            $document->setActionName($block['actionName']);
        }

        $this->manager->persist($document);

        // set properties
        if (isset($block['properties'])) {
            foreach ($block['properties'] as $propName => $prop) {
                if (is_array($prop)) {
                    foreach ($prop as $lang => $translatedProp) {
                        $class->reflFields[$propName]->setValue($document, $translatedProp);
                    }
                    $this->manager->bindTranslation($document, $lang);
                } else {
                    $class->reflFields[$propName]->setValue($document, $prop);
                }

            }
        }
        // create children
        if (isset($block['children'])) {
            foreach ($block['children'] as $childName => $child) {
                $this->loadBlock($document, $childName, $child);
            }
        }
    }

    private function getIdentifier($document)
    {
        $class = $this->manager->getClassMetadata(get_class($document));

        return $class->getIdentifierValue($document);
    }
}
