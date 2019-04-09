<?php

namespace App\Document\Block;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ContainerBlock;

/**
 * @PHPCRODM\Document(referenceable=true, translator="child")
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class FeatureBlock extends ContainerBlock
{
    public function getType()
    {
        return 'app.blocks.feature';
    }
}
