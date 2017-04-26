<?php // if ( ! defined('COUPE') ) exit('No direct script access allowed');

/**
* PHP Coupé
*
* Desenvolvimento PHP rápido e reaproveitável
*
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/2.5/br/ [(CC) by-nd]
* @link http://www.samuelcorradi.net/phpcoupe
*/

/**
* Classe \Coupe\View
*
* Métodos de view
*
* @version 0.1
* @package Coupe
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/coupe/view
*/

namespace Coupe;

abstract class View
{
	
	/**
	* Array associativo que armazena
	* dados que serão exibidos na view.
	* O array é associativo e o nome
	* da posição deve ser o nome da
	* variável impresso na view.
	* @acess public
	* @var array Dados passados para a view
	*/
	public $data = array();
	
	/**
	* Classe construtora protegida.
	*
	* @access public
	* @param string $filepath Caminho para o arquivo.
	* @param array $data Array associativo que passa valores para a view.
	* @return void
	*/
	public function __construct(Array $data=NULL)
	{
		
		$this->data = $data;
	
	}

	public function __set($k, $v)
	{

		$this->set($k, $v);

	}

	public function __get($k)
	{

		return $this->get($k);

	}

	public function __isset($k)
	{

		return isset($this->data[$k]);

	}

	public function __unset($k)
	{

		if ( ! isset($this->data[$k]) )
		{
			throw new InvalidArgumentException("Unable to unset the field '$k'.");
		}

		unset($this->data[$k]);

		return $this;

	}
	
	/**
	* Ação quando a classe for tratada
	* como string.
	* @access public
	* @return string Conteúdo do arquivo renderizado.
	* @see \Coupe\View::reder()
	*/
	final public function __toString()
	{
		
		return $this->render();
	
	}
	
	/**
	* Adiciona um item as variáveis da view.
	* @final
	* @param string|array $item String da chave associada ao valor ou array associativo com chaves e valores.
	* @param mixed $value Valor associado a chave passada.
	* @return void
	*/
	final public function set($item, $value=NULL)
	{
		
		if( is_array($item) )
		{
			foreach( $item as $k => $v )
			{
				$this->set($k, $v);
			}
		}
		else
		{
			$this->data[ $item ] = $value;
		}
		
	}

	/**
	* Pega um item da view.
	* @access public
	* @final
	* @param string $item Chave associativa para recuperar valor.
	* @return mixed Valor armazena na chave ou nulo caso chave não exista.
	*/
	final public function get($k)
	{

		if ( ! isset($this->data[ $k ]) )
		{
			throw new InvalidArgumentException("Unable to get the field '$k'.");
		}

		$field = $this->data[ $k ];

		return $field instanceof Closure ? $field($this) : $field;
		
	}

	abstract function render();
	
}

?>
