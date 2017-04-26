<?php

include_once BASE . 'plugins/auth/libraries/Habilis/Auth.php';

include_once BASE . 'plugins/auth/libraries/Habilis/Acl.php';

include_once BASE . 'plugins/auth/libraries/Habilis/Auth/Adapter/Model.php';

include_once BASE . 'plugins/auth/libraries/Habilis/Auth/Storage/Session.php';

class AuthController extends AppController
{
	
	/**
	* Endereço para quando logar.
	*/
	public $onauth = 'http://localhost/phpcoupe/outro';
	
	/**
	* Endereço para quando deslogar.
	*/
	public $onlogout = 'http://localhost/phpcoupe/';
	
	/**
	* Armazena o objeto de autenticação.
	*/
	protected $_auth;

	/**
	* Se foi pedido para fazer o login, e retornar true,
	* redireciona para página inicial definida em
	* AuthComponent::_param_onauth.
	*/
	public function beforeAction()
	{

		$storage = new \Habilis\Auth\Storage\Session();
		
		$this->_auth = new Auth($storage);
		
		$acl = new \Habilis\Acl;
		
		if( isset($_POST['auth_submit']) && ! $storage->isActive() )
		{
			
			$adapter = new \Habilis\Auth\Adapter\Model(coupe()->model('User'));
			
			$this->_auth->setIdentity('User.email', $_POST['login']);
			
			$this->_auth->setIdentity('Client_client.service_id', $_POST['group']);
			
			$this->_auth->setCredential('User.password', $_POST['pass']);
			
			if( $this->_auth->authenticate($adapter) )
			{
				coupe()->redirect(new \Habilis\Url($this->onauth));
			}
			
		}
		
	}
	
	/**
	*
	*/
	public function login()
	{

		if ( $this->_auth->getStorage()->isActive() )
		{
			coupe()->redirect(new \Habilis\Url($this->onauth));
		}
	}
	
	/**
	*
	*/
	public function logout()
	{
		
		$this->_auth->getStorage()->clearData();
		
		coupe()->redirect(new \Habilis\Url($this->onlogout));
	
	}

}
