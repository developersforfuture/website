<?php

namespace App\Document;

use App\Services\Utils;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use PHPCR\NodeInterface;

/**
 * @PHPCRODM\Document(referenceable=true)
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class Speaker
{
    /**
     * @var string
     *
     * @PHPCRODM\Id
     */
    protected $id;
    /**
     * @var NodeInterface
     *
     * @PHPCRODM\Node
     */
    protected $node;
    /**
     * @var NodeInterface
     *
     *  @PHPCRODM\ParentDocument
     */
    protected $parentDocument;
    /**
     * @var string
     *
     * @PHPCRODM\Nodename
     */
    protected $name;
    /**
     * @var string
     *
     * @PHPCRODM\Field(type="string")
     */
    public $speakerName;
    /**
     * @var string
     *
     * @PHPCRODM\Field(type="string")
     */
    public $twitter;
    /**
     * @var string
     *
     * @PHPCRODM\Field(type="string")
     */
    public $image;
    /**
     * @var string
     *
     * @PHPCRODM\Field(type="string")
     */
    public $bio;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return NodeInterface
     */
    public function getParentDocument(): NodeInterface
    {
        return $this->parentDocument;
    }

    /**
     * @param NodeInterface $parentDocument
     */
    public function setParentDocument(NodeInterface $parentDocument): void
    {
        $this->parentDocument = $parentDocument;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSpeakerName(): string
    {
        return $this->speakerName;
    }

    /**
     * @param string $name
     */
    public function setSpeakerName(string $name): void
    {
        $this->speakerName = $name;
    }

    /**
     * @return string
     */
    public function getTwitter(): string
    {
        return $this->twitter;
    }

    /**
     * @param string $twitter
     */
    public function setTwitter(string $twitter): void
    {
        $this->twitter = $twitter;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getBio(): string
    {
        return $this->bio;
    }

    /**
     * @param string $bio
     */
    public function setBio(string $bio): void
    {
        $this->bio = $bio;
    }

    /**
     * @return NodeInterface
     */
    public function getNode(): NodeInterface
    {
        return $this->node;
    }

    /**
     * @param NodeInterface $node
     */
    public function setNode(NodeInterface $node): void
    {
        $this->node = $node;
    }

    /**
     * @PHPCRODM\PrePersist()
     */
    public function prePersist(): void
    {
        if (null !== $this->getSpeakerName()) {
            $this->setName(Utils::createSeoUrl($this->getSpeakerName()));
        }
    }

    /**
     * @PHPCRODM\PreUpdate()
     */
    public function preUpdate(): void
    {
        if (null !== $this->getSpeakerName()) {
            $this->setName(Utils::createSeoUrl($this->getSpeakerName()));
        }
    }
}
