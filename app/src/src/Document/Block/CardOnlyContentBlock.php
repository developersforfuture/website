<?php

namespace App\Document\Block;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * @PHPCRODM\Document(referenceable=true, translator="child")
 *
 * @author Leo Maroni <leo@labcode.de>
 */
class CardOnlyContentBlock extends StringBlock
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


    public function getType()
    {
        return 'app.blocks.cardOnlyContent';
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
}
