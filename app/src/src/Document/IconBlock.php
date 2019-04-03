<?php

namespace App\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;

/**
 * @PHPCRODM\Document(referenceable=true, translator="child")
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class IconBlock extends SimpleBlock
{
    /**
     * @var string
     *
     * @PHPCRODM\Field(property="icon_class")
     */
    private $iconClass;
    /**
     * @var string
     *
     * @PHPCRODM\Field(property="tile_lenght")
     */
    private $tileLength;

    public function getType()
    {
        return 'app.blocks.icon';
    }

    /**
     * @return string
     */
    public function getIconClass(): string
    {
        return $this->iconClass;
    }

    /**
     * @param string $iconClass
     */
    public function setIconClass(string $iconClass): void
    {
        $this->iconClass = $iconClass;
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
