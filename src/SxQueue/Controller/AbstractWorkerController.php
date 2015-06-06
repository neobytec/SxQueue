<?php

namespace SxQueue\Controller;

use SxQueue\Controller\Exception\WorkerProcessException;
use SxQueue\Exception\ExceptionInterface;
use SxQueue\Worker\WorkerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use SxQueue\Options\WorkerOptions;

/**
 * AbstractController
 */
abstract class AbstractWorkerController extends AbstractActionController
{
    /**
     * @var WorkerInterface
     */
    protected $worker;

    /**
     * Process a queue
     *
     * @return string
     * @throws WorkerProcessException
     */
    public function processAction()
    {
        $options = $this->params()->fromRoute();
        $queue   = $options['queue'];
        
        $this->loadWorker($queue);
        
        try {
            $result = $this->worker->processQueue($queue, $options);
        } catch (ExceptionInterface $e) {
            throw new WorkerProcessException(
                'Caught exception while processing queue',
                $e->getCode(),
                $e
            );
        }

        return sprintf(
            "Finished worker for queue '%s' with %s counts\n",
            $queue,
            $result
        );
    }
    
    /**
     * Load worker for a specific queue
     * 
     * @param string $queue
     */
    private function loadWorker($queue)
    {
        $config = $this->serviceLocator->get('Config');

        $options = !empty($config['sx_queue']['worker'][$queue])
            ? $config['sx_queue']['worker'][$queue]
            : $config['sx_queue']['worker']['default'];
        
        $this->worker = $this->serviceLocator->get('SxQueueDoctrine\Worker\DoctrineWorker');
        $this->worker->setOptions(new WorkerOptions($options));
    }
}
