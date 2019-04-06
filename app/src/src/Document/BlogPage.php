<?php

namespace App\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @PHPCRODM\Document(referenceable=true)
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class BlogPage extends StaticPage
{
    /**
     * @var Speaker
     *
     * @PHPCRODM\Child
     */
    protected $speaker;
    /**
     * @var Date
     *
     * @PHPCRODM\Field(type="date", property="jcr:created", nullable=true)
     */
    protected $createdAt;

    /**
     * @return Speaker
     */
    public function getSpeaker(): ?Speaker
    {
        return $this->speaker;
    }

    /**
     * @param Speaker $speaker
     */
    public function setSpeaker(Speaker $speaker): void
    {
        $this->speaker = $speaker;
    }

    /**
     * @return Date
     */
    public function getCreatedAt(): Date
    {
        return $this->createdAt;
    }

    /**
     * @param Date $createdAt
     */
    public function setCreatedAt(Date $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
