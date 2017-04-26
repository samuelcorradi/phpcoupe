<?php

/**
* Arquivos com funções comuns que
* ajudam no debug ou desenvolvimento
* de qualquer aplicação em PHP.
*/



/**
* Retorna a instancia da classe principal do programa.
*
* @return \Coupe\Core Instância da classe \Coupe\Core.
*/
function coupe()
{

	return \Coupe::getInstance();

}

/**
* Imprime uma variável para análise.
*
* @param mixed $var Variável a ter o conteúdo exibido.
* @return string Conteúdo de variável em formato HTML.
*/
function pr($var)
{

	echo '<pre>' . print_r($var, true) . '</pre>';

}

/**
* Imprime a estrura de um array.
*
* @param mixed $var Variável a ter o conteúdo exibido.
* @return string Extrutura detalhada de uma variável em formato HTML.
*/
function dump($var)
{

    pr(var_export($var, true));
    
}

/**
* Se uma variável for verdadeira,
* retorna-a com alguma formatação especifica.
*
* @param mixed $var Variável que se quer verificar se é verdadeira.
* @param string $string String a ser retornada.
* @return string|false String se a variável for verdadeira ou falso.
*/
function if_string($variable, $string)
{

	if( $variable && $variable!='' )
	{
		return $string;
	}

}

/**
* Remove determina posição de um array.
* A operação é feita com o array passado por referência,
* e retorna o item removido.
*
* @param array $array Array passado por referência
* @param mixed $index Posição a ser removida
* @return mixed Posição removida do array
*/
if( ! function_exists('array_unset') )
{

	function array_unset(& $array = array(), $index)
	{

		$item = $array[$index];

		unset($array[$index]);

		return $item;

	}

}

/**
* Alternativa para a função get_called_class() do PHP 5.2.
*
* @return string Nome da classe onde a função foi chamada.
*/
if( ! function_exists('get_called_class') )
{

	function get_called_class()
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

}

