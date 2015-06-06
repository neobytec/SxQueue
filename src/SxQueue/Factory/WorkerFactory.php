<?php
namespace SxQueue\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * WorkerFactory
 */
class WorkerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $canonicalName = null, $requestedName = null)
    {
        $workerOptions      = $serviceLocator->get('SxQueue\Options\WorkerOptions');
        $queuePluginManager = $serviceLocator->get('SxQueue\Queue\QueuePluginManager');

        return new $requestedName($queuePluginManager, $workerOptions);
    }
}
