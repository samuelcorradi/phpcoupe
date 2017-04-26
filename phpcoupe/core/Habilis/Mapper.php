<?php

/**
* Classe \Habilis\Mapper armazena e faz tradução de rotas.
*
* Classe Mapper possui métodos responsáveis por armazenar
* e gerenciar mapas rotas (URLs). A classe também faz
* a tradução de URLs, onde uma determinada URLs pode ter
* outra associada a ela e usar essa associação para criar
* redirecionamentos.
*
* @version 0.1
* @package Habilis
* @subpackage Navigation
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/habilis/mapper
*/

namespace Habilis;

class Mapper
{

	/**
	* Armazena array associativo (dicionário)
	* com lista de mapas de rotas onde, a
	* chave do array é o caminho solicitado
	* e o conteúdo da chave é o caminho que
	* deverá ser utilizado.
	* @access private
	* @var array Dicionário de rotas.
	*/
	private $__map = array();
	
	/**
	* Classe construtora.
	*
	* @access public
	* @param array $maps Array associativo com rotas a serem usadas onde a chave é o caminho requisitado e o valor o caminho para redirecionamento.
	* @return void
	* @see \Habilis\Mapper::connect()
	*/
	public function __construct(Array $maps=NULL)
	{
		if( is_array($maps) )
		{
			foreach($maps as $map => $route)
			{
				$this->connect($map, $route);
			}
		}
	}

	/**
	* Retorna a tradução da rota usando
	* os mapas de rotas para fazer essa
	* tradução. Caso não haja uma tradução
	* para o caminho passado, retornará
	* falso.
	* 
	* @access public
	* @param string $path Rota que possui um mapa associado.
	* @return string|false String se haver uma tradução, ou falso caso não haja.
	* @see \Habilis\Mapper::$__map
	*/
	public function match($path)
	{
		
		foreach((array)$this->__map as $map => $route)
		{
			
			$pattern = "/^" . str_replace(array("/", "%any", "%part", "%num"), array("\/", "(.*)", "([^\/]*)", "([0-9]+)"), $map) . "\/?$/";
			
			if( preg_match($pattern, $path) )
			{
				return preg_replace($pattern, $route, $path);
			}
			
		}
		
		return FALSE;
		
	}
	
	/**
	* Adiciona um mapa de rota em tempo de execução.
	*
	* @access public
	* @param string $map Rota que será mapeada para um caminho real.
	* @param string $route Caminho real para o recurso.
	* @return bool Retorna sempre verdadeiro.
	* @see \Habilis\Mapper::$__map
	*/
	public function connect($map, $route)
	{
		
		$this->__map[ $map ] = $route;
		
		return TRUE;
		
	}
	
	/**
	* Remove um mapa de rota em tempo de execução.
	*
	* @access public
	* @param string $route Rota a ser removida.
	* @return bool Retorna sempre verdadeiro.
	* @see \Habilis\Mapper::$__map
	*/
	public function disconnect($map)
	{
		
		unset($this->__map[ $map ]);
		
		return TRUE;
		
	}
	
}

?>
