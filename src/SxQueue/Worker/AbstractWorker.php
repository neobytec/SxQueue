<?php

namespace SxQueue\Worker;

use SxQueue\Job\JobInterface;
use SxQueue\Options\WorkerOptions;
use SxQueue\Queue\QueueInterface;
use SxQueue\Queue\QueuePluginManager;
use SxQueue\Queue\QueueAwareInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * AbstractWorker
 */
abstract class AbstractWorker implements WorkerInterface, EventManagerAwareInterface
{
    /**
     * @var QueuePluginManager
     */
    protected $queuePluginManager;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var bool
     */
    protected $stopped = false;

    /**
     * @var WorkerOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param QueuePluginManager $queuePluginManager
     * @param WorkerOptions      $options
     */
    public function __construct(QueuePluginManager $queuePluginManager, WorkerOptions $options)
    {
        $this->queuePluginManager = $queuePluginManager;
        $this->options            = $options;

        // Listen to the signals SIGTERM and SIGINT so that the worker can be killed properly. Note that
        // because pcntl_signal may not be available on Windows, we needed to check for the existence of the function
        if (function_exists('pcntl_signal')) {
            declare(ticks = 1);
            pcntl_signal(SIGTERM, array($this, 'handleSignal'));
            pcntl_signal(SIGINT, array($this, 'handleSignal'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function processQueue($queueName, array $options = array())
    {
        /** @var $queue QueueInterface */
        $queue        = $this->queuePluginManager->get($queueName);
        $eventManager = $this->getEventManager();
        $count        = 0;

        $workerEvent = new WorkerEvent($queue);
        $eventManager->trigger(WorkerEvent::EVENT_PROCESS_QUEUE_PRE, $workerEvent);

        while (true) {
            // Check for external stop condition
            if ($this->isStopped()) {
                //TODO: Realizar tareas de mantenimiento
                break;
            }

            $job = $queue->pop($options);

            // The queue may return null, for instance if a timeout was set
            if (!$job instanceof JobInterface) {
                // Check for internal stop condition
                if ($this->isMaxMemoryExceeded()) {
                    break;
                }
                $count++;
                continue;
            }

            $workerEvent->setJob($job);

            $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB_PRE, $workerEvent);

            $this->processJob($job, $queue);
            $count++;

            $eventManager->trigger(WorkerEvent::EVENT_PROCESS_JOB_POST, $workerEvent);

            if ($this->isMaxRunsReached($count) || $this->isMaxMemoryExceeded()) {
                break;
            }

        }

        $eventManager->trigger(WorkerEvent::EVENT_PROCESS_QUEUE_POST, $workerEvent);

        return $count;
    }

    /**
     * {@inheritDoc}
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            get_called_class(),
            'SxQueue\Worker\WorkerInterface'
        ));

        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }

    /**
     * Check if the script has been stopped from a signal
     *
     * @return bool
     */
    public function isStopped()
    {
        return $this->stopped;
    }

    /**
     * Did worker exceed the threshold for memory usage?
     *
     * @return bool
     */
    public function isMaxMemoryExceeded()
    {
        return memory_get_usage() > $this->options->getMaxMemory();
    }

    /**
     * Is the worker about to exceed the threshold for the number of jobs allowed to run?
     *
     * @param $count current count of executed jobs
     * @return bool
     */
    public function isMaxRunsReached($count)
    {
        return $count >= $this->options->getMaxRuns();
    }

    /**
     * Handle the signal
     *
     * @param int $signo
     */
    public function handleSignal($signo)
    {
        switch($signo) {
            case SIGTERM:
            case SIGINT:
                $this->stopped = true;
                break;
        }
    }
}
