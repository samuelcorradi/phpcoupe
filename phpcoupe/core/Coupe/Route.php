<?php

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
* Classe \Coupe\Route
*
* Classe basicamente recebe um caminho de URL
* e diz o que é o controller, a action,
* a view, etc.
*
* @version 0.1
* @package Coupe
* @subpackage Navigation
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/coupe/route
* @see \Habilis\Mapper
*/

namespace Coupe;

class Route
{
	
	/**
	* Recebe uma rota qualquer e retorna suas
	* partes em forma de um array.
	*
	* @access public
	* @static
	* @param string $path String de uma rota.
	* @return mixed Rota em formato de array ou NULO.
	*/
	public static function toArray($route)
	{
		
		$array = array();
		
		$parts = explode('/', trim($route, '/'));
		
		foreach((array)$parts as $segment)
		{
			if( $segment )
			{
				$array[] = $segment;
			}
			else
			{
				break;
			}
		}
		
		return ( empty($array) ) ? NULL : $array;
		
	}
	
	/**
	* Trata uma rota retornando-a no formato correto.
	*
	* @access public
	* @see self::toArray()
	* @static
	* @param string $path String de uma rota.
	* @return string Rota devidamente tratada.
	*/
	public static function sanetize($path)
	{
		
		return '/' . implode('/', (array)self::toArray($path));
	
	}
	
	/**
	* No caso de não achar uma action
	* na rota, usar o nome aqui definido.
	* @access public
	* @var string Ação padrão.
	*/
	public $default_action = 'index';
	
	/**
	* Lista de prefixos que podem ser usados na rota.
	* @access public
	* @var array Lista de prefixos usados.
	*/
	public $prefixes = array();
	
	/**
	* Armazena a rota.
	* @access private
	* @var string Caminho da rota.
	*/
	private $__path;
	
	/**
	* Armazena a lista de mapas de rotas em um objeto do tipo \Habilis\Mapper.
	* @access private
	* @var \Habilis\Mapper Objeto que armazena mapas de rotas.
	*/
	private $__mapper;
	
	/**
	* Método construtor.
	*
	* @param mixed $path String ou objeto URL que contém o caminho da rota.
	* @access public
	* @see \Coupe\Route::setPath()
	*/
	public function __construct($path)
	{
		
		$this->setPath($path);

	}
	
	/**
	* Adicionar o objeto de mapas
	* para posterior tradução de rota.
	*
	* @access public
	* @see \Coupe\Route::$__mapper
	* @param \Habilis\Mapper $mapper Objeto com mapas de rotas.
	*/
	public function setMapper(\Habilis\Mapper & $mapper)
	{
		
		$this->__mapper = $mapper;
	
	}
	
	/**
	* Retorna o objeto de mapas de rotas.
	*
	* @access public
	* @see \Coupe\Route::$__mapper
	* @return \Habilis\Mapper Objeto de mapas armazenado.
	*/
	public function getMapper()
	{
		
		return $this->__mapper;
		
	}
	
	/**
	* Define o caminho da rota.
	*
	* @access public
	* @param string $path Define o caminho da rota.
	* @see \Coupe\Route::$__path
	*/
	public function setPath($path)
	{
		
		$this->__path = (string)$path;

	}
	
	/**
	* Pega conteúdo da variável que armazena o caminho.
	*
	* @access public
	* @see \Coupe\Route::$__path
	* @return string Caminho da rota.
	*/
	public function getPath()
	{
		
		return $this->__path;

	}
	
	/**
	* Pega a rota devidamente tratada e
	* traduzida caso haja um mapa para ela.
	*
	* @access public
	* @see \Coupe\Route::$__path
	* @see \Habilis\Mapper::match()
	* @see self::sanetize()
	* @return string Retorna a rota.
	*/
	public function get()
	{
		
		$route = $this->__path;
		
		if( $this->__mapper )
		{
			
			$map = $this->__mapper->match($this->__path);
		
			if( $map )
			{
				$route = $map;
			}
			
		}
		
		return self::sanetize($route);
		
	}
	
	/**
	* Pega o caminho em forma de array.
	*
	* @access public
	* @see self::toArray()
	* @see \Coupe\Route::get()
	* @return array|null Array com os trechos do caminho ou NULL.
	*/
	public function getAsArray()
	{
		
		$path = $this->get();
		
		return self::toArray($path);
		
	}
	
	/**
	* Adiciona novos possíveis prefixos ao caminho.
	* É possível passar um ou mais prefixos separados
	* como argumentos, ou um único array com a lista
	* de prefixos a serem usados.
	*
	* @access public
	* @see func_get_args()
	* @see \Coupe\Route::$prefixes
	*/
	public function setPrefix($prefix)
	{
		
		$prefixes = ( is_array($prefix) ) ? $prefix : func_get_args();
		
		foreach($prefixes as $prefix)
		{
			$this->prefixes[] = $prefix;
		}
		
	}
	
	/**
	* Pega o prefixo da rota.
	*
	* @access public
	* @see \Coupe\Route::$prefixes
	* @see \Coupe\Route::getAsArray()
	* @return string|null String se usado um prefixo definido ou NULL.
	*/
	public function getPrefix()
	{
		
		$parsed = $this->getAsArray();
		
		if( ! empty($parsed) )
		{
			if( in_array($parsed[0], (array)$this->prefixes) )
			{
				return $parsed[0];
			}
		}
		
	}
	
	/**
	* No caso de se usar MVC, retorna
	* o que seria o nome do controller.
	*
	* @access public
	* @see \Coupe\Route::getAsArray()
	* @see \Coupe\Route::getPrefix()
	* @return string|null String se usado o nome de um controller ou NULL.
	*/
	public function getController()
	{	

		$parsed = $this->getAsArray();
		
		if( ! empty($parsed) )
		{
			if( $this->getPrefix() )
			{
				array_shift($parsed);
			}
			if( ! empty($parsed) )
			{
				return $parsed[0];
			}
		}
		
	}
	
	/**
	* Retorna o caminho para a View.
	*
	* @access public
	* @see \Coupe\Route::getAsArray()
	* @see \Coupe\Route::getParameters()
	* @return string Caminho para view.
	*/
	public function getView()
	{
		
		$parsed = (array)$this->getAsArray();

		$parameters = $this->getParameters();

		$view_array = array_slice($parsed, 0, count($parsed) - count($parameters));
		
		return implode('/', $view_array);

	}

	/**
	* No caso de se usar MVC, retorna
	* o que seria o nome do action.
	*
	* @access public
	* @see \Coupe\Route::$default_action
	* @see \Coupe\Route::getAsArray()
	* @see \Coupe\Route::getPrefix()
	* @return string Nome da action.
	*/
	public function getAction()
	{
		
		$parsed = (array)$this->getAsArray();
		
		foreach(array('getPrefix', 'getController') as $method)
		{
			if( $this->{$method}() )
			{
				array_shift($parsed);
			}
		}
		
		if( empty($parsed) )
		{
			$action = $this->default_action;
		}
		else
		{
			$action = $parsed[0];
		}
		
		$prefix = $this->getPrefix();
		
		if( $prefix )
		{
			$action = $prefix . '_' . $action;
		}
		
		return $action;
		
	}
	
	/**
	* No caso de se usar MVC, retorna
	* o que seria a lista de parâmetros
	* passados via URL. Os parâmetros
	* são tudo que vem no caminho que
	* não seja prefixo, controller ou 
	* action.
	*
	* @access public
	* @see \Coupe\Route::getAsArray()
	* @return array|null Lista de parametros ou NULO.
	*/
	public function getParameters()
	{	
		
		$parsed = $this->getAsArray();
		
		if( ! empty($parsed) )
		{
			
			foreach(array('getPrefix', 'getController', 'getAction') as $method)
			{				
				if( $this->{$method}() )
				{
					array_shift($parsed);
				}
			}
			if( ! empty($parsed) )
			{
				
				return $parsed;
			
			}

		}
		
	}
	
}

?>
