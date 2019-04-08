<?php

namespace App\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\ContentBundle\Doctrine\Phpcr\StaticContent;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @PHPCRODM\Document(referenceable=true)
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class Document extends StaticContent
{
    /**
     * @var string
     *
     * @PHPCRODM\Field()
     */
    protected $link;
    /**
     * @var string
     *
     * @PHPCRODM\Field()
     */
    protected $description;
    /**
     * @var string[]
     *
     * @PHPCRODM\Field(assoc="array", multivalue=true)
     */
    protected $categories;
    /**
     * @var Date
     *
     * @PHPCRODM\Field(type="date", property="jcr:created", nullable=true)
     */
    protected $createdAt;

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param string[] $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }
}
