<?php

namespace Habilis\Http;

/**
 * Class Header constroi cabeçalhos para
 * requisições HTTP. Sintaxe dos campos:
 * <Name>: <opt[=value]>[; <subopt[=value]>, ...]
 *
 * @package Habilis\Http
 */
class Header
{

	/*
	* Constante que define a autenticação básica.
	*/
    const AUTH_BASIC  = 'BASIC';

	/*
	* Constante que define a autenticação no
	* modo digest.
	*/
    const AUTH_DIGEST = 'DIGEST';  // ainda não implementado.

	/**
	 * @var array
	 */
	protected $_fieldlist = array();

	/*************************************
	 * Estático
	 *************************************/

	/**
	 * Configura o objeto através de uma string
	 * de cabeçalho.
	 *
	 * @param $s
	 * @return bool|static
	 */
	public static function fromString($s)
	{

		$l = explode("\r\n", $s);

		if ( ! is_array($l) || count($l)==1)
		{
			$l = explode("\n", $s);
		}

		$header = new static();

		foreach($l as $line)
		{
			try
			{
				$header->set($line);
			}
			catch(\Exception $e)
			{
				return FALSE;
			}
		}

		return $header;

	}

	/**
	 * Cria um objeto de resposta baseado
	 * no array global $_SERVER.
	 */
	public static function fromServerVar()
	{

		$resp = new \Habilis\Http\Response();

	}

	/**
	 * Faz o trabalho inverso do método self::createKeyName().
	 * Recebe o nome de uma chave de um campo e transforma
	 * em nome de campo, substituindo os caracteres de
	 * espaçamento '_' por '-' e colocando as iniciais em
	 * maiúsculo. Ex.: "cache_control" gera "Cache-Control".
	 *
	 * @param string $k Nome da chave.
	 * @return string Nome do campo obtido a partir do nome da chave.
	 */
	final protected static function createHeaderNameFromKey($k)
	{

		$e = explode('_', $k);

		$e = array_map(function($n) {

			return ucfirst($n);

		}, $e);

		return implode('-', $e);

	}

	/*
	* Método estático que trata cabeçalhos
	* que definem preferências (como Accept)
	* retorna um array ordenado por ordem
	* de preferencia.
	*/
	private static function ___headerValueParser($p)
	{

		$accept = array();

		foreach( preg_split('/\s*,\s*/', $p) as $i => $t )
		{

			$o = new \stdclass;

			$o->pos = $i;

			if( preg_match(",^(\S+)\s*;\s*(?:q|level)=([0-9\.]+),i", $t, $m) )
			{
				$o->type = $m[1];

				$o->q = (double)$m[2];
			}
			else
			{
				$o->type = $t;

				$o->q = 1;
			}

			$accept[] = $o;

		}

		usort($accept, function($a, $b)
		{

			$diff = $b->q - $a->q;

			if($diff>0)
			{
				$diff = 1;
			}
			elseif($diff<0)
			{
				$diff = -1;
			}
			else
			{
				$diff = $a->q - $b->q;
			}

			return $diff;

		});

		$accept_data = array();

		foreach ($accept as $a)
		{
			$accept_data[] = $a->type;
		}

		return $accept_data;

	}

	/*************************************
	 * Métodos mágicos
	 *************************************/

	/*
	* Transforma o objeto em string.
	*/
	public function __toString()
	{

		return $this->toString();

	}

	// TODO: nome de referencia a atributos via -> não pode ter "-". E o nome dos campos deve ser case sensitive. Olhar essa transformacoes de nome que aconecem em self::get e self:: set.

	public function __set($name, $value)
	{

		return $this->set($name, $value);

	}

	public function __get($name)
	{

		return $this->get($name);

	}

	/*************************************
	 * Métodos
	 *************************************/

	/**
	 * Adiciona campos ao cabeçalho.
	 * Pode ser informado uma string no formato
	 * "Nome-Campo: Valor", ou o nome do campo
	 * com seu valor as opções. Caso o valor
	 * passado for um array, o terceiro parametro
	 * de opções é ignorado.
	 *
	 * @param string $field_name
	 * @param mixed $value
	 * @param mixed $opt
	 * @return bool
	 * @throws \Exception
	 */
	public function set($field_name, $value=NULL, $opt=NULL)
	{

		if(preg_match('/^([^()><@,;:\"\\/\[\]?=}{ \t]+):(.*)$/', $field_name, $match) && $value===NULL) // '/^(?P<name>[^()><@,;:\"\\/\[\]?=}{ \t]+):(.*)$/'
		{
			return $this->set($match[1], $match[2]);
		}
		elseif( $value===NULL )
		{
			throw new \Exception("Header name was provided without a value", 1);
		}

		$field_value = $this->renderValue($value);

		/*
		 * Se for um array de valores não
		 * executa pois não saberá em qual
		 * valor se refere a lista de opcões.
		 */
		if( ! is_array($value) && $opt!==NULL )
		{
			$field_value .= $this->renderOptions($opt);
		}

		/*
		 * Concatena com algum valor já existente.
		 * Caso não haja, a virgula que separa
		 * ficaria no inicio, então usa-se ltrim()
		 * para remove-la.
		 */
		$this->_fieldlist[ $this->createKeyName($field_name) ] = ltrim($this->get($field_name) . ', ' . $field_value, ", ");

		return TRUE;

	}

	/**
	 * Retorna um parametro a partir do nome do
	 * campo do cabeçalho. Antes transforma o
	 * nome no formato de posição do array e
	 * nome de função.
	 *
	 * @param string $name Nome do parametro.
	 * @return string
	 */
	public function get($name)
	{

		$k = $this->createKeyName($name);

		if( array_key_exists($k, $this->_fieldlist) )
		{
			return $this->_fieldlist[ $k ];
		}

		return FALSE;

	}

	/**
	 * Limpa o cabeçado.
	 *
	 * @return $this
	 */
	public function clear()
	{

		$this->_fieldlist = array();

		return $this;

	}

	/**
	* Remove um determinado campo do cabeçalho.
	*/
	public function remove($name)
	{

		unset($this->_fieldlist[ self::createHeaderNameFromKey($name) ]);

	}

	/**
	* Testa se o cabeçalho possui
	* um determinado campo.
	*/
	public function has($name)
	{

		return array_key_exists($this->createKeyName($name), $this->_fieldlist);

	}

	/**
	 * Faz a leitura do array que armazena os
	 * campos do cabeçalho e retorna como
	 * string.
	 *
	 * @access public
	 * @return string
	 */
	public function toString()
	{

		$header = '';

		foreach( $this->_fieldlist as $k => $v )
		{
			$header .= self::createHeaderNameFromKey($k) . ': ' . $v . "\r\n";
		}

		return $header;

	}

	/*************************************
	 * Métodos protegidos
	 *************************************/

	/**
	 * Método apenas para formatar
	 * o par de chave e valor usado
	 * como valores dos campos do
	 * cabeçalho.
	 *
	 * @param $value
	 * @param null $key
	 * @return string
	 */
	final protected function renderValue($value)
	{

		if( is_array($value) )
		{

			$list = array();

			foreach($value as $k => $v)
			{
				$list[] = ( is_numeric($k) ) ? (string)$v : "{$k}={$v}";
			}

			return implode(',', $list);

		}

		return (string)$value;

	}

	/**
	 * Método apenas para formatar
	 * o par de chave e valor usado
	 * como opcoes nos valores dos
	 * campos do cabeçalho.
	 *
	 * @param $value
	 * @param null $key
	 * @return string
	 */
	final protected function renderOptions($value)
	{

		if( is_array($value) )
		{

			$string = '';

			foreach($value as $k => $v)
			{
				$string .= ( is_numeric($k) ) ? "; $v" : "; {$k}={$v}";
			}

			return $string;

		}

		return "; {$value}";

	}

	/**
	 * Essa classe usa o nome de um campo do cabeçalho
	 * para criar uma chave do array que armazena os
	 * valores. Então esse método de uso interno serve
	 * para receber o nome de um campo do cabeçalho e
	 * substituir alguns tipos de caracteres de espaço
	 * por '_' (que é o padrão de nomeclatura para chaves de
	 * matrizes) e criar o nome de chave do array baseando-se
	 * no nome do campo. Recebe uma string que substitui
	 * tudo com '-', '_', ' ' e '.' por '_' (underline).
	 * Ex: O nome do campo "Accept-Language" gerará a chave
	 * "accept_language" que será usada para referenciar
	 * os valores deste campo. "Cache-Control" para
	 * "cache_control", etc.
	 *
	 * @param string $name Nome do campo.
	 * @return string Chave para armezenar o valor para o campo.
	 */
	final protected function createKeyName($name)
	{

		$e = preg_split('/[\-\_\s\.]/', $name);

		return strtolower(implode('_', $e));

	}


}

?>