<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'SxQueue\Job\JobPluginManager'     => 'SxQueue\Factory\JobPluginManagerFactory',
            'SxQueue\Options\WorkerOptions'    => 'SxQueue\Factory\WorkerOptionsFactory',
            'SxQueue\Queue\QueuePluginManager' => 'SxQueue\Factory\QueuePluginManagerFactory'
        ),
    ),
    'sx_queue' => array(
        'worker' => array(
            'max_runs'   => 100,
            'max_memory' => 100 * 1024 * 1024
        ),
    ),
);
