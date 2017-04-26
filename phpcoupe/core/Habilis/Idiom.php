<?php

/**
* A classe \Habilis\Idiom permite o controle
* de idiomas em site permitindo armazenar
* em cookies ou sessão a opção de idioma
* escolhido, permitindo a tradução de programas.
*
* A classe \Habilis\Idiom é responsável por
* gerenciar opções de idiomas. A classe usa
* a ISO 639-1 para identificar os idiomas.
*
* @version 0.1
* @package Habilis
* @subpackage Internationalization
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/habilis/idiom
*/

namespace Habilis
{
	
	class Idiom
	{
	
		/**
		* Gera uma instância da classe.
		*
		* @access public
		* @final
		* @return \Habilis\Idiom Singleton da classe.
		* @static
		*/
		public final static function getInstance()
		{
		
			static $inst = array();
		
			if( ! isset($inst[0]) )
			{
				$inst[0] = new \Habilis\Idiom();
			}
		
			return $inst[0];
		
		}
	
		/**
		* Chave associativa usado na setagem do idioma
		* via POST ou GET. O programa altera o idioma
		* sempre que verifica que essa chave está presente
		* em POST ou GET.
		* @access public
		* @var string Chave usada para identificar a submissão do idima pelo cliente. 
		*/
		public $key = 'idiom';
	
		/**
		* Armazena a lista de idiomas utlizados.
		* O idoma é alterado via GET ou POST
		* quando o valor dele esteja presente
		* como chave associativa nesse array.
		* O conteúdo de cada posição deve
		* ser igual ao nome do idioma escrito
		* na sua forma nativa (pode ser usado
		* para gerar um menu de idiomas).
		* @access private
		* @var array Dicionário com os idiomas utilizados.
		*/
		private $__idiom_list = array(
		'en'=>'english',
		'pt'=>'português',
		'es'=>'español'
		);
	
		/**
		* Função construtora.
		* Verifica se houve uma postagem de idioma
		* via POST ou GET e seta um cookie que
		* armazenará a opção.
		*
		* @access public
		* @see \Habilis\Idioma::$__idiom_list
		* @see \Habilis\Idioma::$key
		*/
		protected function __construct()
		{
			
			if( isset($_REQUEST[ $this->key ]) && array_key_exists($_REQUEST[ $this->key ], $this->__idiom_list) )
			{
				setcookie($this->key, $_REQUEST[ $this->key ], time() + 60 * 60 * 24 * 5);
			}
			
		}

		/**
		* O acesso privado ao método __clone()
		* previne que o objeto seja duplicado
		* preservando a característica de
		* Singleton da classe.
		*
		* @access private
		* @final
		*/
		private final function __clone() { trigger_error('Clone is not allowed.', E_USER_ERROR); }
	
		/**
		* Pega a lista de idiomas definidos.
		*
		* @final
		* @access public
		* @return array Lista de idiomas definidos.
		* @see \Habilis\Idioma::$__idiom_list
		*/
		public final function getList()
		{

			return $this->__idiom_list;

		}

		/**
		* Limpa as configurações de idioma.
		*
		* @access public
		* @return bool Sempre retorna verdadeiro.
		* @see \Habilis\Idioma::$key
		*/
		public function clear()
		{

			unset($_REQUEST[ $this->key ]);

			setcookie($this->key);

			return TRUE;
		
		}

		/**
		* Retorna o idioma corrente.
		* Se tem um idioma setado no Cookie, retorna ele.
		* Ou se CPIdiom::$__current está setado, retorna ele.
		* Em último caso retorna a opção CPIdiom::$_default.
		*
		* @access public
		* @return string Idoma corrente
		* @see \Habilis\Idioma::$key
		*/
		public function get()
		{
	
			if( isset($_REQUEST[ $this->key ]) )
			{
				return $_REQUEST[ $this->key ];
			}
			elseif( isset($_COOKIE[ $this->key ]) )
			{
				return $_COOKIE[ $this->key ];
			}
			
		}
	
	}
	
}
