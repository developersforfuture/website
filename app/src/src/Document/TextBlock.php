<?php

namespace App\Document;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * @PHPCRODM\Document(referenceable=true, translator="child")
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class TextBlock extends StringBlock
{

    /**
     * @var string
     *
     * @PHPCRODM\Field(property="tile_lenght")
     */
    private $tileLength;

    public function getType()
    {
        return 'app.blocks.text';
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
}
