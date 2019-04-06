<?php

namespace App\Document;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock;
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
        return 'app.blocks.text';
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
