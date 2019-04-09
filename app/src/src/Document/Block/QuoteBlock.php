<?php

namespace App\Document\Block;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * @PHPCRODM\Document(referenceable=true)
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class QuoteBlock extends TextBlock
{
    /**
     * @var string
     *
     * @PHPCRODM\Field(property="tile_lenght")
     */
    protected $source;

    public function getType()
    {
        return 'app.blocks.quote';
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source): void
    {
        $this->source = $source;
    }

}
