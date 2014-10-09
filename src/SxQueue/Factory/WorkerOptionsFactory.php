<?php

namespace SxQueue\Factory;

use SxQueue\Options\WorkerOptions;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WorkerOptionsFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        return new WorkerOptions($config['sx_queue']['worker']);
    }
}
