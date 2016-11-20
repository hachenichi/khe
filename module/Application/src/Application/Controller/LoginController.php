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
use Zend\Session\Container;

class LoginController extends AbstractActionController
{
    public function indexAction()
    {
		$this->layout('layout/login');
		
		$varsToView = array();
		$varsToView["error"] = false;
		$userSessionAuth = new Container('userSessionAuth');
		
		if(!$userSessionAuth->offsetExists('loggedIn')){// Valida si hay session
		
			if($this->getRequest()->isPost()){//Si viene por post
				
				if($this->getRequest()->getPost('email') && $this->getRequest()->getPost('email') !== '' 
					&& $this->getRequest()->getPost('password') && $this->getRequest()->getPost('password') !== ''){
						
						$email = strtolower($this->getRequest()->getPost('email'));
						$password = $this->getRequest()->getPost('password');
						
						$emailValidator = new \Zend\Validator\EmailAddress();
						if ($emailValidator->isValid($email)) {//Valida si el email es valido
							
							$dbquery = $this->getServiceLocator()->get('Application/Model/Dbquery');
							
							$userExist = $dbquery->login($email, $password);
							
							//Debug::dump($userExist);
							
							if($userExist){// Validamos si existe en la DB
								
								$userSessionAuth->offsetSet('loggedIn', true);
								$userSessionAuth->offsetSet('userBasicInfo', $userExist);
								$userSessionAuth->offsetSet('users_id', $userExist["users_id"]);
								$userSessionAuth->offsetSet('users_fullname', $userExist["users_fullname"]);
								$userSessionAuth->offsetSet('users_id', $userExist["users_email"]);
								
								return $this->redirect()->toRoute( 'application');
								
							}else{
								
								$varsToView["error"] = true;
								$varsToView["vvv"] = "gggg";
								//echo '<br>usuario no esta en db';
							}
							
							//echo '<br>email valido';
						}else{
							
							$varsToView["error"] = true;
							$varsToView["vvv"] = "gggg";
							
							print_r($varsToView);
							die("fin");
							//echo '<br>email invalido';
						}
					
					}
			}
		
		}
		
		
		
        return new ViewModel();
    }
}
