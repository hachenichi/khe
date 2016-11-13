<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Debug\Debug;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Application\view\helper\ImagesPagination;
use Application\view\helper\UltimasPagination;
use Zend\EventManager\EventInterface;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		
		$application = $e->getParam('application');
        $config = $e->getApplication()->getServiceManager()->get('Config');
		$viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
		
		//$sessionConfig = new SessionConfig();
		//$sessionConfig->setOptions($config['session']);
		//$sessionManager = new SessionManager($sessionConfig);
		//$sessionManager->start();
		
		//Container::setDefaultManager($sessionManager);
		
		//$userSessionAuth = new Container('userSessionAuth');
		$viewModel->setVariables(array(
            'layout' => $config["layout"],
            //'loggedIn' => $userSessionAuth->offsetExists('loggedIn')
        ));
		
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

	/*
	public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
				'imagespagination' => function() {
					return new \Application\View\Helper\ImagesPagination();
                },
				'ultimaspagination' => function() {
					return new \Application\View\Helper\UltimasPagination();
                },
				'breadcrumbs' => function() {
					return new \Application\View\Helper\Breadcrumbs();
                },
            ),
        );
    }*/
}
