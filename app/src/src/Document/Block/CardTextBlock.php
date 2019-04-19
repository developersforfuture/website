<?php

namespace App\Document\Block;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * @PHPCRODM\Document(referenceable=true, translator="child")
 *
 * @author Leo Maroni <leo@labcode.de>
 */
class CardTextBlock extends StringBlock
{

    /**
     * @var string
     *
     * @PHPCRODM\Field(property="tile_length")
     */
    private $tileLength;

    /**
     * @var string
     *
     * @PHPCRODM\Field(property="tile_class")
     */
    private $tileClass;

    /**
     * @var string
     *
     * @PHPCRODM\Field(property="card_title")
     */
    private $cardTitle;

    /**
     * @var string
     *
     * @PHPCRODM\Field(property="button_text")
     */
    private $buttonText;

    /**
     * @var string
     *
     * @PHPCRODM\Field(property="button_link")
     */
    private $buttonLink;


    public function getType()
    {
        return 'app.blocks.cardText';
    }

    /**
     * @return string
     */
    public function getTileLength(): string
    {
        return $this->tileLength;
    }

    /**
     * @param string $tileLength
     */
    public function setTileLength(string $tileLength): void
    {
        $this->tileLength = $tileLength;
    }

    /**
     * @return string
     */
    public function getTileClass(): string
    {
        return $this->tileClass;
    }

    /**
     * @param string $tileClass
     */
    public function setTileClass(string $tileClass): void
    {
        $this->tileClass = $tileClass;
    }

    /**
     * @return string
     */
    public function getCardTitle(): string
    {
        return $this->cardTitle;
    }

    /**
     * @param string $cardTitle
     */
    public function setCardTitle(string $cardTitle): void
    {
        $this->cardTitle = $cardTitle;
    }

    /**
     * @return string
     */
    public function getButtonText(): string
    {
        return $this->buttonText;
    }

    /**
     * @param string $buttonText
     */
    public function setButtonText(string $buttonText): void
    {
        $this->buttonText = $buttonText;
    }

    /**
     * @return string
     */
    public function getButtonLink(): string
    {
        return $this->buttonLink;
    }

    /**
     * @param string $buttonLink
     */
    public function setButtonLink(string $buttonLink): void
    {
        $this->buttonLink = $buttonLink;
    }
}
