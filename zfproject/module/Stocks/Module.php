<?php
namespace Stocks;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Stocks\Model\Stocks;
use Stocks\Model\StocksTable;
use Stocks\Model\Scrip;
use Stocks\Model\ScripTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;


class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    

    // Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Stocks\Model\StocksTable' =>  function($sm) {
                    $tableGateway = $sm->get('StocksTableGateway');
                    $table = new StocksTable($tableGateway);
                    return $table;
                },
                'StocksTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Stocks());
                    return new TableGateway('stocks', $dbAdapter, null, $resultSetPrototype);
                },
                'Stocks\Model\ScripTable' =>  function($sm) {
                    $tableGateway = $sm->get('ScripTableGateway');
                    $table = new ScripTable($tableGateway);
                    return $table;
                },
                'ScripTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Scrip());
                    return new TableGateway('scrip', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
    
}