<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MenuController extends AbstractActionController
{
    public function indexAction()
    {
		$this->layout('layout/front');
		
        return new ViewModel();
    }
	
    public function tourAction()
    {
		$this->layout('layout/front');
		
		
		$dbquery = $this->getServiceLocator()->get('Application/Model/Dbquery');
		
		$users=$dbquery->getUsers();
		
		
		//print_r($users);
		//die("fff");
		
        return new ViewModel();
    }
}
