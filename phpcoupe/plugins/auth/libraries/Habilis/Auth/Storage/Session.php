<?php

namespace Habilis\Auth\Storage;

include_once BASE . 'plugins/auth/libraries/Habilis/Auth/Storage.php';

class Session implements \Habilis\Auth\Storage
{

	/**
	 * Nome da posição dos dados
	 * dentro da sessão. Se um
	 * nome não for definido,
	 * usa-se o padrão: 'auth_session'.
	 */
	public $namespace = 'auth_session';
	
	/**
	 * Método construtor do storage
	 * utilizando sessão deve receber
	 * uso de um namespace.
	 *
	 * @param string $namespace
	 */
	public function __construct($namespace=NULL)
	{

		if( $namespace )
		{
			$this->namespace = (string) $namespace;
		}

		@ session_start();

	}
	
	public function isActive()
	{

		return isset($_SESSION[ $this->namespace ]);

	}

	public function setData(Array $data)
	{

		foreach($data as $k => $v)
		{
			$_SESSION[ $this->namespace ][ $k ] = (string)$v;
		}

		return $this;

	}

	public function getData()
	{

		if( isset($_SESSION[ $this->namespace ]) )
		{
			return $_SESSION[ $this->namespace ];
		}
		else
		{
			throw new \Exception("There was a problem reading the storage.");
		}
		
	}

	public function clearData()
	{
	
		unset($_SESSION[ $this->namespace ]); // remove a sessao de nome definido nas propriedades do objeto

		return TRUE;

	}
	
}

?>
