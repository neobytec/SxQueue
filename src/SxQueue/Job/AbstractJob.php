<?php

namespace SxQueue\Job;

use Zend\Stdlib\Message;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * This class is supposed to be extended. To create a job, just implements the missing "execute" method. If a queueing
 * system needs more information, you can extend this class (but for both Beanstalk and SQS this is enough)
 */
abstract class AbstractJob extends Message implements JobInterface, ServiceLocatorAwareInterface
{

    const JOB_STATUS_PENDING    = 1;
    const JOB_STATUS_RUNNING    = 2;
    const JOB_STATUS_DELETED    = 3;
    const JOB_STATUS_FAILED     = 4;
    const JOB_STATUS_COMPLETED  = 5;
    const JOB_STATUS_UNKNOWN    = 6;


    /**
     * @var string|array|null
     */
    protected $content = null;

    protected $queue;
    
    protected $result = self::JOB_STATUS_UNKNOWN;
    protected $serviceLocator;


    /** 
     * ServiceLocatorAwareInterface
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }


    /** 
     * ServiceLocatorAwareInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }


    public function getResult()
    {
        return $this->result;
    }


    public function setResult($status)
    {
        $this->result = $status;
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->setMetadata('id', $id);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->getMetadata('id');
    }

    /**
     * The 'class' attribute that is saved allow to easily handle dependencies by pulling the job from
     * the JobPluginManager whenever it is popped from the queue
     *
     * @return string
     */
    public function jsonSerialize()
    {
        $data = array(
            'class'    => get_called_class(),
            'content'  => serialize($this->getContent()),
            'metadata' => $this->getMetadata(),
        );

        return json_encode($data);
    }

    /**
     * Get the queue for the job.
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Set the que for the job.
     * 
     * @param string $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
        return $this;
    }
}
