<?php

namespace SxQueue\Queue;

use SxQueue\Job\JobPluginManager;

/**
 * AbstractQueue
 */
abstract class AbstractQueue implements QueueInterface
{
    /**
     * @var JobPluginManager
     */
    protected $jobPluginManager;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string           $name: Nombre de la cola
     * @param JobPluginManager $jobPluginManager: Gestor de las clases de tareas
     */
    public function __construct($name, JobPluginManager $jobPluginManager)
    {
        $this->name             = $name;
        $this->jobPluginManager = $jobPluginManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getJobPluginManager()
    {
        return $this->jobPluginManager;
    }

    /**
     * Create a concrete instance of a job
     *
     * @param  string $className
     * @param  mixed  $content
     * @param  array  $metadata
     * @return \SxQueue\Job\JobInterface
     */
    public function createJob($className, $content = null, array $metadata = array())
    {
        /** @var $job \SxQueue\Job\JobInterface */
        $job = $this->jobPluginManager->get($className);

        $job->setContent(unserialize($content));
        $job->setMetadata($metadata);

        if ($job instanceof QueueAwareInterface) {
            $job->setQueue($this);
        }
        
        return $job;
    }
}
