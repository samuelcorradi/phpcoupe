<?php

/**
* Classe \Habilis\Datasource gerência
* conexões a bases de dados.
*
* Armazena configurações de conexão
* genérica para ser usado em aplicações
* que necessitam se conectar a uma fonte
* de dados.
*
* @version 0.1
* @package Habilis
* @subpackage Database
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/habilis/datasource
*/

namespace Habilis;
	
abstract class Datasource
{

	/**
	* Armazenas as configurações de datasource.
	* @access protected
	* @var array
	* @static
	*/
	static protected $_config;

	/**
	* Adiciona uma nova configuração de datasource.
	*
	* @access public
	* @param string $name Nome do datasource.
	* @param array $attr Configuração do datasource.
	* @return bool Verdadeiro indicando a atribuição da configuração.
	* @static
	*/
	public static function set($name, Array $attr)
	{
	
		return self::$_config[ $name ] = $attr;

	}
	
	/**
	* Retorna a configuração de datasource solicitada.
	*
	* @access public
	* @param string $name Nome do datasoruce.
	* @return array Configuraçãoes do datasource indicado.
	* @static
	*/
	public static function get($name)
	{
		if( isset(self::$_config[ $name ]) )
		{
			return self::$_config[ $name ];
		}
	}
	
	/**
	* Retorna a instância do datasource
	* especificado baseando-se nos
	* parâmetros definidos para ele em self::set.
	*
	* @access public
	* @final
	* @param string $name Nome do datasource.
	* @return object Datasource da classe utilizada.
	* @see self::get
	* @see self::getClass
	* @static
	*/
	final public static function factory($name)
	{

		static $inst = array();

		$attr = self::get($name);

		if( $attr )
		{

			if( ! isset($inst[ $name ]) )
			{
				
				$class = self::getClass();
				
				$inst[ $name ] = new $class($name, $attr);
			
			}

			return $inst[ $name ];

		}

	}
	
	/**
	* Antes do PHP 5.3, era mais complicado
	* pegar o nome da classe de forma estática.
	*
	* @access public
	* @static
	* @return string Nome da classe atual.
	* @see debug_backtrace
	* @see get_called_class
	* @see get_class
	*/
	public static function getClass()
	{
		
		if( ! function_exists('get_called_class') )
		{
			
			$traces = debug_backtrace();
			
			foreach ($traces as $trace)
			{
				if ( isset($trace['object']) )
				{
					if ( is_object($trace['object']) )
					{
						return get_class($trace['object']);
					}
				}
			}
			
		}
		else
		{
			return get_called_class();
		}
			
	}
	
	/**
	* Nome do datasource.
	* @access protected
	* @var string Nome do datasource.
	*/
	protected $_name;
	
	/**
	* Não pode ser acessada publicamente.
	* Sua execução é feita através do método
	* self::factory.
	*
	* @access protected
	* @final
	* @param string $name Nome do datasource.
	* @return void
	*/
	final protected function __construct($name)
	{

		$this->_name = $name;
		
		$this->connect();

	}
	
	/**
	* Previne que o objeto seja duplicado.
	*
	* @access private
	* @return void
	*/
	final private function __clone() { trigger_error('Clone is not allowed.', E_USER_ERROR); }
	
	/**
	* Retorna as configurações do
	* datasource em self::$_config como
	* propriedades do objeto.
	*
	* @access public
	* @param string $key Nome da propriedade.
	* @return mixed Valor da propriedade se estiver definida ou NULL.
	*/
	public function __get($key)
	{
		if( isset(self::$_config[ $this->_name ][ $key ]) )
		{
			return self::$_config[ $this->_name ][ $key ];
		}
	}
	
	/**
	* Pega o nome do datasource.
	*
	* @access public
	* @final
	* @return string Nome do datasource.
	*/
	final public function getName()
	{
		
		return $this->_name;
		
	}
	
	/**
	* Método de conexão que deve ser implementado
	* nas classes derivadas.
	*
	* @abstract
	* @access public
	*/
	abstract public function connect();
	
	/**
	* Método de desconexão que deve ser implementado
	* nas classes derivadas.
	*
	* @abstract
	* @access public
	*/
	abstract public function disconnect();

}
