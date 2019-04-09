<?php

namespace App\Document\Block;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class HeadlineBlock extends StringBlock
{
    /**
     * @var string
     */
    protected $size;

    public function getType()
    {
        return 'app.blocks.headline';
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    /**
     * @param string $size
     */
    public function setSize(string $size): void
    {
        $this->size = $size;
    }
}
