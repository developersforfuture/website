<?php

namespace App\Document\Block;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\StringBlock;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * @PHPCRODM\Document(referenceable=true, translator="child")
 *
 * @author Leo Maroni <leo@labcode.de>
 */
class ContainerBlock extends \Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\ContainerBlock
{

    public function getType()
    {
        return 'app.blocks.container';
    }

}
