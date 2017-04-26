<?php

/**
* A classe \Habilis\Html gera strings no formato HTML.
*
* Essa classe permite criar uma extrutura recursiva
* de objetos que representam tags HTML com seus
* valores e propriedades. Esses objetos são
* programados para serem convertidos em string
* no formato padrão HTML.
*
* @version 0.1
* @package Habilis
* @subpackage Html
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @since 04/05/2012
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/habilis/html
*/

namespace Habilis;

class Html
{

	/**
	* Transforma uma string no formato HTML em array.
	*
	* @access public
	* @static
	* @param string $html String no formato HTML
	* @return array Estrutura e paramatros do HTML em formato Array.
	* @see preg_match
	* @see preg_match_all
	* @see strpos
	* @see substr
	* @see trim
	*/
	static public function toArray($string)
	{

		$elements_regex = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*?)<(\/\s*\1\s*)>)/s';

		preg_match_all($elements_regex, $string, $elements_array);

		if( ! empty($elements_array) )
		{

			$array = array();

			$attributes_regex = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';

			foreach ( $elements_array[1] as $e_key => $e_value )
			{

				$array[ $e_key ]["tag"] = $e_value;

				if ( ($p = strpos($elements_array[3][ $e_key ], "<")) > 0 )
				{
					$array[ $e_key ]["text"] = substr($elements_array[3][ $e_key ], 0, $p); // $array[ $e_key ]["text"] = substr($elements_array[3][ $e_key ], 0, $p - 1);
				}

				if ( ($attributes_string = trim($elements_array[2][ $e_key ])) )
				{

					preg_match_all($attributes_regex, $attributes_string, $attributes_array);

					foreach ( $attributes_array[1] as $a_key => $a_value )
					{
						$array[ $e_key ]["attributes"][$a_value] = $attributes_array[2][$a_key];
					}

				}

				if ( preg_match($elements_regex, $elements_array[3][ $e_key ]) )
				{
					$array[ $e_key ]["childrens"] = $elements_array[3][ $e_key ]; // $array[ $e_key ]["childrens"] = CPHtml::toArray($elements_array[3][ $e_key ]);
				}
				elseif ( isset($elements_array[3][ $e_key ]) )
				{
					$array[ $e_key ]["text"] = $elements_array[3][ $e_key ];
				}

				$array[ $e_key ]["closetag"] = $elements_array[4][ $e_key ];

			}

			return $array;

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
	* Tag.
	* @access public
	* @var string
	*/
	protected $_tag;
	
	/**
	* Atributos da tag armazenados
	* em forma de array associativo.
	* @access public
	* @var array
	*/
	protected $_attr;
	
	/**
	* Armazena elementos contidos dentro
	* da tag em um array como uma pilha.
	* Os elementos ficam armazenados
	* nesse array numérico e sequencia.
	* Ao imprimir o objeto, seus nós
	* são lidos e impressos como string. É
	* possível adicionar outro objeto do
	* \Habilis\Html como nó do objeto atual
	* fazendo a recursividade.
	* @access public
	* @var array
	*/
	protected $_text = array();
	
	/**
	* Construtor recebe uma string HTML ou apenas a tag HTML.
	* Se o parser self::toArray for executado com sucesso,
	* entende-se que uma string no formato HTML foi informado.
	* Caso contrário, entende-se que apenas a tag HTML foi
	* informada.
	*
	* @access public
	* @param string $tag [optional] Tag ou string no formato HTML
	* @param array $attr [optional] Atributos da tag HTML
	*/
	public function __construct($tag=NULL, Array $attr=NULL)
	{
		
		$parsed = self::toArray($tag);

		if( $parsed )
		{

			$calledclass = self::getClass();
	
			foreach($parsed as $each)
			{
				
				$html = new $calledclass($each['tag']);
			
				if( isset($parsed[0]['text']) )
				{
					$html->setText($each['text']);
				}

				if( isset($each['childrens']) )
				{
					
					$subcphtml = new $calledclass($each['childrens']);
					
					$html->setText($subcphtml);
				
				}

				if( isset($each['attributes']) && is_array($each['attributes']) )
				{
					foreach($each['attributes'] as $attr => $v)
					{
						$html->setAttr($attr, $v);
					}
				}
			
				$this->_text[] = $html;

			}

		}
		else
		{
			$this->_tag = $tag;
		}

		/*
		* Se foi passado o array de parâmetros o adiciona,
		* mesmo que os parâmetros já tenham sido definidos
		* nas rotinas acima.
		*/
		if( ! empty($attr) )
		{
			$this->_attr = $attr;
		}

	}

	/**
	* Dá saída no formato HTML.
	*
	* @access public
	* @return string Objeto e suas propriedades transformados no formato HTML.
	* @see htmlspecialchars_decode
	*/
	public function __toString()
	{

		/*
		* Pega conteúdo se tiver
		*/
		$html = implode('', $this->_text);

		/*
		* Abre tag, se tiver
		*/
		if( $this->_tag )
		{

			/*
			* Gerando parametros
			*/
			$attr = '';

			if( ! empty($this->_attr) )
			{

				foreach($this->_attr as $k => $v)
				{

					if ( is_array($v) )
					{
						$v = implode(' ', $v);
					}
					elseif( $v === true )
					{
						$v = $k;
					}

					if ( $v || $v===0 )
					{
						$attr .= " {$k}=\"{$v}\"";
					}

				}

			}

			if( ! empty($this->_text) )
			{
				$html = "<{$this->_tag}{$attr}>$html</{$this->_tag}>\n";
			}
			else
			{
				$html = "<{$this->_tag}{$attr} />\n";
			}

		}

		return htmlspecialchars_decode($html);

	}

	public function __set($k, $v)
	{

		$this->setAttr($k, $v);

		return $this;

	}

	public function __get($k)
	{

		return $this->getAttr($k);

	}

	public function __isset($k)
	{

		return isset($this->_attr[ $k ]);

	}

	public function __unset($k)
	{

		if ( ! isset($this->_attr[$k]) )
		{
			throw new \Exception("Unable to unset the field '$k'.");
		}

		unset($this->_attr[$k]);

		return $this;

	}

	/**
	 * @param $tag
	 * @return $this
	 */
	public function setTag($tag)
	{

		$this->_tag = $tag;

		return $this;

	}

	/**
	 * @return null|string
	 */
	public function getTag()
	{

		return $this->_tag;

	}

	/**
	* Atalho para adição de nós no formato \Habilis\Html.
	* Funciona como uma factory para criar objetos do
	* próprio tipo da classe já adicionando-os como nó
	* HTML do objeto atual.
	*
	* @access public
	* @param string $tag [optional] Tag ou string no formato HTML
	* @return object Referencia do objeto do tipo \Habilis\Html criado.
	*/
	public function & children($tag=NULL, Array $attr=NULL)
	{

		$html = new \Habilis\Html($tag, $attr);

		$this->setText($html);

		return $html;

	}

	/**
	* Adiciona um nó ao HTML. Essa função pode
	* ser invocada mais de uma única vez sendo
	* que, a cada execução, o valor é adicionado
	* um após o outro. A impressão dos valores
	* será feita respeitando a ordem que os
	* forem adicionados.
	* Esse método retorna o valor de $this (o
	* próprio objeto) para que possar fazer
	* chamadas encadeadas
	*
	* @access public
	* @param mixed $node Qualquer valor que possa ser convertido em string ou uma lista armazenados em um array numérico.
	* @return object O próprio objeto (o valor de $this).
	*/
	public function & setText($node)
	{

		if( ! is_array($node) )
		{
			$this->_text[] = $node;
		}
		else
		{
			$this->_text = $node;
		}

		return $this;

	}

	/**
	* Pega o valor da tag HTML. Valor de uma
	* tag HTML é todo conteúdo que está entre
	* a abertura e fechamento da tag.
	* @access public
	* @return array Array que armazena a pilha de valores da tag.
	*/
	public function getText()
	{

		return $this->_text;

	}

	/**
	* Limpa o array self::$nodes removendo
	* o valor da tag.
	*
	* @access public
	* @return bool Verdadeiro indicado a limpeza dos nós.
	*/
	public function clearText()
	{

		return $this->_text = array();

	}
	
	/**
	* Adiciona um atributo ao HTML.
	*
	* @access public
	* @param mixed $key Chave do atributo ou array associativo já contendo todos atributos.
	* @param mixed $value Valor a ser usado como atributo. Caso a chave seja um array é ignorado.
	* @return bool Verdadeiro indicado a declaração do atributo.
	*/
	public function setAttr($key, $value=NULL)
	{
		
		if( is_array($key) )
		{
			$this->_attr = $key;
		}
		else
		{
			$this->_attr[ $key ] = $value;
		}

		return $this;
		
	}
	
	/**
	* Pega o atributo do HTML caso ele exista.
	*
	* @access public
	* @param string $key Nome do atributo HTML.
	* @return mixed Valor do atributo ou nulo caso ele não exista.
	*/
	public function getAttr($k)
	{

		if ( isset($this->_attr[ $k ]) )
		{

			$field = $this->_attr[ $k ];

			return $field instanceof Closure ? $field($this) : $field;

		}

	}

	/**
	* Limpa o array self::$attr removendo
	* os atributos da tag.
	*
	* @access public
	* @return bool Verdadeiro indicado a limpeza das propriedades.
	*/
	public function clearAttr()
	{

		return $this->_attr = array();

	}
	
}

?>
