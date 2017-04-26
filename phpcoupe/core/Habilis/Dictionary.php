<?php

namespace Habilis;

/**
 * A classe \Habilis\Dictionary permite carregar dicionários de palavras.
 *
 * A classe \Habilis\Dictionary permite carregar arquivos
 * que armazenam palavras usando uma chave como associação.
 * Dessa forma, é possível carregar as palavras/frases no
 * projeto usando as chaves associativas, e trocar o
 * dicionário quando quiser, alterando todo idioma do
 * projeto de forma transparente.
 *
 * @version 0.1
 * @package Habilis
 * @subpackage Internationalization
 * @author Samuel Corradi <falecom@samuelcorradi.com.br>
 * @copyright Copyright (c) 2012, habilis.com.br
 * @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
 * @example http://www.habilis.com.br/documentation/classes/habilis/dictionary
 * @see \Habilis\Idiom
 */
class Dictionary
{

	/**
	* Lista que armazena o caminho
	* dos arquivos já carregados para
	* não serem carregados novamente.
	*
	* @access private
	* @var array Caminho dos arquivos já carregados.
	*/
	private $__loaded = array();

	/**
	* Dicionário (array associativo)
	* que armazena o conteúdo dos
	* arquivos de dicionários carregados.
	*
	* @access private
	* @var array Conteúdo dos dicionários carregados.
	*/
	private $__dictionary = array();

	/**
	* Carrega os arquivos de
	* idioma para dentro da variável
	* \Habilis\Dictionary::$__dictionary
	* ou retorna o array $lang do arquivo
	* de idioma solicitado para ser
	* usado em tempo de execução.
	*
	* @access public
	* @param string $filepath Caminho para o arquivo.
	* @param bool $return [optional] Verdadeiro para retornar o array $lang contido no arquivo de dicionário solicitado.
	* @return mixed Verdairo se o arquivo foi carregado, array se foi pedido para retornar o array $lang, nulo se o arquivos não existir ou falso caso ocorra algum erro.
	* @see \Habilis\Dictionary::$__dictionary
	* @see \Habilis\Dictionary::$__loaded
	*/
	function load($filepath, $return=FALSE)
	{

		if ( ! $return && in_array($filepath, $this->__loaded, TRUE) ) /* Verifica se o arquivo jah estah carregado. */
		{
			return TRUE;
		}

		if ( ! is_file($filepath) )
		{
			return NULL;
		}

		if( ! is_readable($filepath) )
		{
			return FALSE;
		}

		include_once($filepath);

		if ( ! isset($lang) ) /* Verifica se há uma variável $lang no arquivo de dicionário carregado. */
		{
			return FALSE;
		}

		if ( $return ) /* Se $return for igual a TRUE, somente retorna o vetor '$lang' do arquivo. */
		{
			return $lang;
		}

		$this->__dictionary = array_merge($this->__dictionary, $lang); /* Adiciona o conteúdo do vetor $lang no vetor de linguagens da classe. */

		$this->__loaded[] = $filepath;

		return TRUE;

	}

	/**
	* Carrega determinada posição do
	* array \Habilis\Dictionary::$__dictionary.
	*
	* @access public
	* @param string $key Chave do array associada a tradução que se quer recuperar.
	* @param mixed $value [optional] Valor que substituirá na frase as marcações propostas pela função sprintf() (%s, %d, etc.).
	* @param mixed $value,... [optional] Conjunto ilimitado de valores que substituirão as marcações propostas pela função sprintf() (%s, %d, etc.).
	* @return string Tradução do dicionário associada a chave passada como primeiro parametro.
	* @see \Habilis\Dictionary::$__dictionary
	* @see call_user_func_array()
	* @see func_get_args()
	* @see sprintf()
	*/
	function get($key, $value=NULL)
	{

		$line = ( isset($this->__dictionary[ $key ]) ) ? $this->__dictionary[ $key ] : FALSE;

		if( $line )
		{

			$args = func_get_args();

			$args[ 0 ] = $line;

			$line = call_user_func_array('sprintf', $args);

		}

		return $line;

	}

}


?>
