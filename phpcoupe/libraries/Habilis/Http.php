<?php

namespace Habilis;

use \Habilis\Http\Header as Header;

/*
* Classe genérica que serve como
* base para as classes de requisição
* e respostas HTTP.
*/
abstract class Http
{

	/*
	* Corpo da mensagem.
	*/
	protected $_body;

	/*
	* 
	*/
	const VERSION_10 = '1.0';

	/*
	* 
	*/
	const VERSION_11 = '1.1';

	/**
	* @var string
	*/
	protected $_version = self::VERSION_11;

	/**
	* Usado par armazenar o cabeçalho.
	*/
	protected $_Header;

	public function __construct()
	{

		$this->_Header = new Header();

	}

	/*
	* Define o método.
	*/
	public function setVersion($v)
	{

		if ( ! defined('static::VERSION_' . $v) )
		{
			throw new \Exception('Invalid HTTP version');
		}

		$this->_version = $v;

		return $this;

	}

	/**
	* Pega a versão do HTTP usado.
	*/
	public function getVersion()
	{

		return $this->_version;
	
	}

	/**
	* Pega o cabeçalho definido para
	* a resposta.
	*/
	public function getHeader()
	{

		return $this->_Header;

	}

	/**
	* Seta o corpo da resposta HTTP.
	*
	* @return string Corpo da resposta HTTP.
	*/
	public function setBody($b)
	{

		$this->_body = (string)$b;

		return $this;

	}

	/**
	* Retorna o corpo da resposta HTTP.
	*
	* @return string Corpo da resposta HTTP.
	*/
	public function getBody()
	{

		return $this->_body;

	}

	/**
	* Retorna o objeto em formato string.
	*/
	public function __toString()
	{

		return $this->toString();

	}

}

?>