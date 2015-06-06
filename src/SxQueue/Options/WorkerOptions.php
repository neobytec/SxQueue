<?php

namespace SxQueue\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * WorkerOptions
 */
class WorkerOptions extends AbstractOptions
{
    /**
     * @var int
     */
    protected $maxRuns;

    /**
     * @var int
     */
    protected $maxMemory;

    /**
     * @var int
     */
    protected $count;
    
    /**
     * @var int
     */
    protected $sleep;
    
    /**
     * @var int
     */
    protected $seconds;

    /**
     * Set how many jobs can be processed before the worker stops
     *
     * @param  int $maxRuns
     * @return void
     */
    public function setMaxRuns($maxRuns)
    {
        $this->maxRuns = (int) $maxRuns;
    }

    /**
     * Get how many jobs can be processed before the worker stops
     *
     * @return int
     */
    public function getMaxRuns()
    {
        return $this->maxRuns;
    }

    /**
     * Set the max memory the worker can use (in bytes)
     *
     * @param  int $maxMemory
     * @return void
     */
    public function setMaxMemory($maxMemory)
    {
        $this->maxMemory = (int) $maxMemory;
    }

    /**
     * Get the max memory the worker can use (in bytes)
     *
     * @return int
     */
    public function getMaxMemory()
    {
        return $this->maxMemory;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        $this->count = (int) $count;
        return $this;
    }

    public function getSleep()
    {
        return $this->sleep;
    }

    public function setSleep($sleep)
    {
        $this->sleep = (int) $sleep;
        return $this;
    }

    public function getSeconds()
    {
        return $this->seconds;
    }

    public function setSeconds($seconds)
    {
        $this->seconds = (int) $seconds;
        return $this;
    }
}
