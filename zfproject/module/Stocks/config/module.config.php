<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Stocks\Controller\Stocks' => 'Stocks\Controller\StocksController',
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'stocks' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/stocks[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Stocks\Controller\Stocks',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    
    
    'view_manager' => array(
        'template_path_stack' => array(
            'stocks' => __DIR__ . '/../view',
        ),
    ),
);