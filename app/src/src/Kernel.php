<?php

namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    /**
     * @var HttpKernelInterface|null
     */
    private $httpCache;

    public function getHttpCache()
    {
        if (!$this->httpCache) {
            $this->httpCache = new SuluHttpCache($this);
        }

        return $this->httpCache;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->setParameter('container.dumper.inline_class_loader', true);

        parent::configureContainer($container, $loader);
    }
}
