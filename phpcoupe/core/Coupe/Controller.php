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
* Classe \Coupe\Controller
*
* A classe Coupé Controller serve como
* base para controllers do framework.
*
* @version 0.1
* @package Coupe
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/coupe/controller
*/

namespace Coupe;

abstract class Controller
{
	
    /**
    * Método estático gera e armazena as instancias
    * de cada classe filha de controller. Cada
    * classe pode ter uma única instância (Singleton).
    *
	* @static
    * @return object Objeto da classe utilizada.
    */
    public static function & getInstance()
	{

		static $inst = array();
		
		$class = self::getClass();
		
		if( ! isset($inst[ $class ]) ) 
		{
			$inst[ $class ] = new $class();
		}
		
		return $inst[ $class ];
	
	}

	/**
	* Antes do PHP 5.3 era mais complicado
	* pegar o nome da classe de forma estática.
	*
	* @static
	* @see get_called_class();
	* @return string Nome da classe atual.
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
	 * Armazena a visão do controller.
	 *
	 * @var
	 */
	public $view;

	/**
	 * @return \Habilis\View
	 */
	public function getView()
	{

		return $this->view;

	}

	/**
	 * @param \Habilis\View $v
	 * @return $this
	 */
	public function setView(\Habilis\View $v)
	{

		$this->view = $v;

		return $this;

	}

	/**
	 * @return string
	 */
	public function render()
	{

		$this->view->set($this->data);

		return $this->render();

	}

	/**
	 * @return string
	 */
	public function __toString()
	{

		return $this->render();

	}

	/**
	* Define o tipo de saída que será
	* utilizado para os dados.
	* Isso irá alterar o cabeçalho HTTP.
	* Pode ser: html, json, xml.
	* O tipo informado pode ser string
	* ou um array, na ordem de prioridade
	* a ser usado, negociando com o cliente.
	* @param mix $doctype String ou Array.
	* @access public
	* @var array Armazena dados submetidos via POST.
	*/
    public $doctype = 'html';

	/**
	* Armazena as informações passadas via POST.
	* @access public
	* @var array Armazena dados submetidos via POST.
	*/
    public $data = array();
    
	/**
	* Armazena os valores que serão passadas para a View.
	* @access public
	* @var array Armazena dados a serem passados para a View.
	*/
    public $set = array();
	
	/**
	* Renderizar a View ou apenas executar a action?
	* @access public
	* @var bool Informa se uma View deve ser carregada após execução do controller.
	*/
	public $auto_render = TRUE;
    
	/**
	* Talvez você queira trocar o identificador do controller.
	* Se ficar em branco usa o nome da classe sem o sufixo
	* 'Controller'. O nome é importante pois pode ser
	* usado para carregar Modelos de forma automática onde
	* o nome do modelo carregado é igual ao identificador do
	* controller.
	* @access protected
	* @var string Nome do controller.
	*/
	protected $name;
	
	/**
	* Método padrão a ser executado caso não tenha sido
	* informado na URL.
	* @access protected
	* @var string Nome da ação padrão do controller.
	*/
	protected $default_action = 'index';
	
	/**
	* Classe construtora protegida.
	*
	* @access protected
	* @final
	* @see get_class()
	* @return void
	*/
	final protected function __construct()
	{
		
        $this->data = $_POST; // define Controller::data com o conteudo da variavel global $_POST
		
		if ( $this->name=='' && preg_match("/(.*)Controller$/", get_class($this), $name) )
		{
			$this->name = $name[1]; // se o nome for igual a '' e conseguiu encontrar um nome...
		}
		
	}
	
	/**
	* Previne que o objeto seja duplicado.
	*
	* @access private
	* @final
	* @return void
	*/
	final private function __clone() { trigger_error('Clone is not allowed.', E_USER_ERROR); }
	
	/**
	* Será executado pela classe \Coupe antes da Action.
	*
	* @access public
	* @return bool
	*/
	public function beforeAction() { return TRUE; }
	
	/**
	* Será executado pela classe \Coupe depois da
	* execução da Action, mas antes da renderização
	* da View.
	*
	* @access public
	* @return bool
	*/
	public function beforeRender() { return TRUE; }
	
	/**
	* Será executado pela classe \Coupe depois
	* da Action e da renderização da View.
	*
	* @access public
	* @return bool
	*/
	public function afterFilter() { return TRUE; }
	
	/**
	* Indica se algo foi passado via POST.
	*
	* @access public
	* @return bool
	*/
	public function isPosted()
	{
		
		return ! empty($this->data);	
	
	}

	/**
	* Adiciona um item a variável que
	* será passada para a View.
	*
	* @param string $item Identificador do dado pra View.
	* @param mix $value [optional] Valor para a View.
	* @access protected
	* @final
	* @return bool
	*/
	final public function set($item, $value=NULL) // TODO verificar por que esse metodo era protegido
	{
		
		$this->set[ $item ] = $value;
		
		return TRUE;	
	
	}

	/**
	* Executa determinada action (método do Controller).
	* O nome da action deve ser passado no primeiro
	* parâmetro. Se não for passado, usa o valor
	* de \Coupe\Controller::$default_action.
	* Os parâmetros a serem passados para essa
	* action (método) devem ser passados no segundo
	* parâmetro como um array não associativo
	* (array('valor1', 'valor2')).
	*
	* @access public
	* @final
	* @param string $action [optional] Nome da action a ser executada.
	* @param array $params [optional] Lista de parâmetros a serem passados para a action.
	* @return bool Verdadeiro se a action foi executada.
	* @see \ReflectionMethod
	*/
	final public function callAction($action=NULL, Array $params=NULL)
	{
		
		if( empty($action) )
		{
			$action = $this->default_action;
		}	
		
		if( ! method_exists($this, $action) )
		{
			return FALSE;
		}
		
		if( method_exists($this, $action) )
		{
		
			$method = new \ReflectionMethod($this, $action); // \ReflectionMethod pega infos do metodo
		
			if( $method->isPublic() )
			{
				
				if( ! $params )
				{
					$params = array();
				}
		
				call_user_func_array(array($this, $action), $params); // chama o método passando os parametros
		
				return TRUE;
		
			}
		
		}
		
	}
	
}