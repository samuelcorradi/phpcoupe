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
* Classe \Coupe
*
* Classe principal do framework.
* Centraliza e gerência todos recursos.
*
* @version 0.7
* @author Samuel Corradi <falecom@samuelcorradi.com.br>
* @copyright Copyright (c) 2012, habilis.com.br
* @license http://creativecommons.org/licenses/by-nd/3.0 Creative Commons BY-ND
* @example http://www.habilis.com.br/documentation/classes/coupe
* @see \Habilis\Config
* @see \Habilis\Url
* @see \Coupe\Controller
* @see \Coupe\Model
* @see \Coupe\View
* @see \Habilis\Dictonary
* @see \Habilis\Log
*/

final class Coupe
{

	/**
	* Retorna uma única instância
	* (Singleton) do framework.
	*
	* @access public
	* @static
	* @return \Coupe Singleton da classe \Coupe.
	*/
	static public function getInstance()
	{

		static $inst = array();

		if( empty($inst) )
		{
			$inst[0] = new \Coupe();
		}

		return $inst[0];

	}
	
	/**
	* Armazena o objeto que
	* faz controle de cache.
	* @access private
	* @var \Coupe\Cache
	*/
	private $_cache;

	/**
	* Classe construtora protegida.
	*
	* @access private
	* @return void
	*/
	private function __construct() { }
	
	/**
	* O acesso privado ao método __clone()
	* previne que o objeto seja duplicado
	* preservando a característica de
	* Singleton da classe.
	*
	* @access private
	*/
	private function __clone() { trigger_error('Clone is not allowed.', E_USER_ERROR); }
	
	/**
	* Retorna objetos \Habilis\Url.
	*
	* @access public
	* @param string $path [optional] Caminho para recurso.
	* @param array $query [optional] Dicionários com parâmetros para URL.
	* @return \Habilis\Url Objeto para manipular URLs.
	*/
	public function url($path='', $query='')
	{

		try
		{
			$friendly = $this->config('uri')->get('url_friendly');
		}
		catch(\Exception $e)
		{
			$friendly = FALSE;
		}


		$url = new \Habilis\Url(\Habilis\Url::current());

		$url->setPath($path);

		$url->setQuery($query);

		/*
		 * Se as URLs amigáveis estiverem desativadas,
		 * passa o caminho para ser uma query de
		 * identificador 'p'.
		 */
		if( $friendly===FALSE )
		{

			/*
			 * Se tiver um caminho, passa ele
			 * para o formato de query usando
			 * o identificador p.
			 */
			if( $path )
			{

				$url->setQuery(array_merge($url->getQueryAsArray(), array('p' => $url->getPath())));

				$url->setPath(BASE_PATH);

			}

		}
		/*
		 * Se as URLs amigáveis estiverem ATIVADAS,
		 * passa a query para fazer parte do caminho.
		 * Usa o formato <identificador>:<valor>.
		 */
		else
		{

			if( BASE_PATH )
			{
				$path = ($path) ? BASE_PATH . "/" . $path : BASE_PATH;
			}

			if( ! empty($query) )
			{
				foreach( $url->getQueryAsArray() as $k => $v )
				{
					$path .= '/' . $k . ':' . $v;
				}
			}

			$url->setPath($path);

			$url->setQuery('');

		}

		return $url;

	}

	/**
	* Carrega arquivos de configurações
	* da aplicação e retorna objeto do
	* tipo \Habilis\Config que permite
	* manupular tais configurações.
	* O método recebe um nome e é
	* tentado carregar o arquivo de
	* configuração de mesmo nome.
	*
	* @access public
	* @param string|array $config [optional] Nome da configuração ou lista com nomes de configurações.
	* @return mixed Falso se o arquivo da configuração solicitada não existir ou instância de \Habilis\Config com as configurações do arquivo encontrado.
	* @see \Habilis\Config
	*/
	public function config($config=NULL)
	{
		
		static $inst = array();
		
		if( ! isset($inst[0]) )
		{
			$inst[0] = new \Habilis\Config();
		}

		if( is_array($config) )
		{
			foreach($config as $each)
			{
				$this->config($each);
			}
		}
		elseif( $config !== NULL )
		{

			$filepath = APP . 'config' . DS . $config . '.php';
			
			if( $inst[0]->load($filepath) )
			{
				return $inst[0];
			}

			throw new \Exception("Configuration file not found!");
		
		}
		
		return $inst[0];
	
	}

	/**
	* Método faz carregamento de arquivos
	* de classes armazenados nas pastas
	* 'libraries' da aplicação, do
	* framework e das pastas de plugin.
	* Pode receber o nome de uma classe
	* (na verdade o nome passado é usado
	* para buscar pelo arquivo) ou uma
	* lista de nomes.
	*
	* @access public
	* @param string|array $name Nome do arquivo com classe ou uma lista de nomes.
	* @return bool Verdadeiro caso todos arquivos sejam carregados ou falso caso o arquivo não seja carregado.
	* @see \Coupe::config()
	*/
	public function lib($name)
	{

		if( is_array($name) )
		{
			
			foreach($name as $lib)
			{
				if ( ! $this->lib((string)$lib) )
				{
					return FALSE;
				}
			}
			
			return TRUE;
			
		}

		$filename = trim(str_replace(array('\\', '/'), DS, $name), DS) . '.php';

		$paths = array(
			APP . 'libraries' . DS,
			BASE . 'libraries' . DS
			);

		$plugins = $this->config()->get('plugins');

		foreach((array)$plugins as $plugin)
		{
			array_unshift($paths, BASE . 'plugins' . DS . $plugin . DS . 'libraries' . DS);
		}

		foreach($paths as $path)
		{

			$filepath = $path . $filename;

			if( file_exists($filepath) )
			{
				require_once $filepath;

				return TRUE;
			}

		}

		return FALSE;

	}

	/**
	* Método responsável por carregar
	* objetos do tipo \Coupe\Controller.
	* Caso a classe controller não
	* tiver sido carregada, o arquivo
	* com a classe controller é buscado
	* primeiro na pasta 'controller' da
	* pasta da aplicação, depois do
	* framework e depois das pasta dos
	* plugins.
	*
	* @access public
	* @param string $name Nome da classe (arquivo) controller sem o sufixo 'Controller'.
	* @return mixed Objeto do tipo \Coupe\Controller ou falso caso o controller não seja encontrado.
	* @see \Coupe::config()
	*/
	public function controller($name)
	{
		
		$classname = $name . 'Controller';	
	
		if( ! class_exists($classname) )
		{
	
			$filename = str_replace('_', DS, $name) . '.php';
			
			$paths = array(
				APP . 'controllers' . DS,
				BASE . 'controllers' . DS
				);
				
			$plugins = $this->config()->get('plugins');
			
			foreach((array)$plugins as $plugin)
			{
				array_unshift($paths, BASE . 'plugins' . DS . $plugin . DS . 'controllers' . DS);
			}
			
			foreach($paths as $path)
			{
				
				$filepath = $path . $filename;
				
				if( file_exists($filepath) )
				{
					require_once $filepath;
						
					return $classname::getInstance();
				}
			}
			
			return FALSE;
	
		}	
			
		return $classname::getInstance();
	
	}

	/**
	* Método responsável por carregar
	* instancia do tipo \Coupe\Model.
	* Caso a classe model não tiver
	* sido carregada, o arquivo
	* com a classe model é buscado
	* primeiro na pasta 'model' da
	* pasta da aplicação, depois do
	* framework e depois das pasta dos
	* plugins.
	*
	* @access public
	* @param string $name Nome da classe (arquivo) model sem o sufixo 'Model'.
	* @return mixed Objeto do tipo \Coupe\Model ou falso caso o model não seja encontrado.
	* @see \Coupe::config()
	*/
	public function model($name)
	{

		$classname = $name . 'Model';	

		if( ! class_exists($classname) )
		{

			$filename = $name . '.php';

			$paths = array(
				APP . 'models' . DS,
				BASE . 'models' . DS
				);

			$plugins = $this->config()->get('plugins');
	
			foreach((array)$plugins as $plugin)
			{
				array_unshift($paths, BASE . 'plugins' . DS . $plugin . DS . 'models' . DS);
			}
	
			foreach($paths as $path)
			{

				$filepath = $path . $filename;

				if( file_exists($filepath) )
				{

					require_once $filepath;

					return $classname::getInstance();

				}
			}

			return FALSE;

		}	

		return $classname::getInstance();

	}
	
	/**
	* Carrega o arquivo de funções.
	* Busca primeiro o arquivo de
	* ajuda na pasta 'helper' da
	* aplicação, depois do framework
	* e depois nas pastas de plugin.
	*
	* @access public
	* @param string $helper Nome do arquivo de ajuda.
	* @return bool Verdadeiro se o arquivo for carregado ou falso se ele não for encontrado.
	* @see \Coupe::config()
	*/
	public function helper($helper)
	{
		
		$paths = array(
			APP . 'helpers' . DS,
			BASE . 'helpers' . DS
			);

		$plugins = $this->config()->get('plugins');
	
		foreach((array)$plugins as $plugin)
		{
			array_unshift($paths, BASE . 'plugins' . DS . $plugin . DS . 'helpers' . DS);
		}
	
		foreach($paths as $path)
		{
			
			$filepath = $path . $helper . '.php';
			
			if( file_exists($filepath) )
			{
				
				require_once $filepath;
				
				return TRUE;
			
			}
			
		}

		$this->log($this->dictionary()->get('helper_notfound', $helper)); // $this->log($this->dictionary->line('helper_notfound', $helper));
		
		return FALSE;
	
	}
	
	/**
	* Retorna o objeto Singleton que
	* gerencia dicionários. O método
	* aceita receber opcionalmente
	* o nome de um arquivo de
	* dicionário como parâmetro para,
	* além de retornar o objeto que
	* gerencia dicionário, carregar um
	* arquivo de dicionário desejado.
	* O arquivo é carregado da pasta
	* 'dictionaries' da aplicação e
	* framework e depois das pastas
	* dos plugins ativos.
	* O método busca primeiro também
	* dentro das pastas usando o
	* idioma definido (ver
	* \Coupe::__getIdiomFolder())
	* Caso o arquivo de dicionário que
	* for passado para ser carregado
	* não for encontrado o método
	* retorna falso.
	* Normamente o método retorna o
	* objeto \Habilis\Dictionary,
	* permitindo usar
	* \Habilis\Dictionary::line()
	* para pegar uma linha do
	* dicionário de forma encadeada.
	*
	* @access public
	* @param strint $name [optional] Nome de arquivo com array de dicionário.
	* @return mixed Sempre a instancia do tipo \Habilis\Dictionary ou falso somente caso o arquivo de dicionário solicitado como parâmetro não tenha sido encontrado. 
	* @see \Coupe::__getIdiomFolder()
	* @see \Coupe::config()
	* @see \Habilis\Dictionary
	*/
	public function dictionary($name=NULL)
	{
		
		static $inst = array();
		
		if( ! isset($inst[0]) )
		{
			$inst[0] = new \Habilis\Dictionary();
		}
		
		if( $name )
		{
	
			$paths = array(
				APP . 'dictionaries' . DS . $this->__getIdiomFolder(),
				BASE . 'dictionaries' . DS . $this->__getIdiomFolder(),
				APP . 'dictionaries' . DS,
				BASE . 'dictionaries' . DS
				);	
			
			
			$plugins = $this->config()->get('plugins');
		
			foreach((array)$plugins as $plugin)
			{
				array_unshift($paths, BASE . 'plugins' . DS . $plugin . DS . 'dictionaries' . DS . $this->__getIdiomFolder(), BASE . 'plugins' . DS . $plugin . DS . 'dictionaries' . DS);
			}
		
			foreach($paths as $path)
			{
				if( $inst[0]->load($path . $name . '.php') )
				{
					return $inst[0];
				}
			}

			return FALSE;
			
		}	
		
		return $inst[0];
	
	}

	/**
	* Retorna um objeto de Conteúdo caso
	* o arquivo de conteúdo realmente
	* exista.
	*
	* @access public
	* @param string $viewid Nome do arquivo de view de conteúdo.
	* @param array $set [optional] Dicionário de valores para a view.
	* @param \Coupe\View $template [optional] Template para ser retornada com o conteúdo anexado a ela.
	* @return mixed Objeto do tipo \Coupe\View ou nulo caso o arquivo de conteúdo não seja encontrado.
	* @see \Coupe::config()
	* @see \Coupe\View
	* @see \Coupe\View::set()
	*/
	public function content($viewid, Array $set=NULL, \Coupe\View $template=NULL)
	{
		
		$viewid = str_replace(array('/', '\\'), DS, $viewid);
		
		$viewid = trim($viewid, DS);
		
		$paths = array(
			APP . 'contents' . DS . $this->__getIdiomFolder(),
			BASE . 'contents' . DS . $this->__getIdiomFolder(),
			APP . 'contents' . DS,
			BASE . 'contents' . DS
			);
		
		$plugins = $this->config()->get('plugins');
	
		foreach((array)$plugins as $plugin)
		{
			array_unshift($paths, BASE . 'plugins' . DS . $plugin . DS . 'contents' . DS . $this->__getIdiomFolder(), BASE . 'plugins' . DS . $plugin . DS . 'contents' . DS);
		}

		foreach( $paths as $path )
		{		

			$content = new \Coupe\View\Template();

			$content->setTemplate($path . $viewid . '.php');
	
			if( is_file($path . $viewid . '.php') )
			{

				$content->set($set);	
	
				if( $template )
				{

					$template->set('content', $content->render());	
				
					return $template;
				
				}
	
				return $content;
	
			}
		
		}
		
		return NULL;
	
	}

	/**
	* Retorna um objeto de Template caso
	* o arquivo da template realmente
	* exista em um dos diretórios.
	* Retonar nulo se o template não for
	* encontrado.
	*
	* @access public
	* @param string $viewid Nome do arquivo de view de conteúdo.
	* @param array $set [optional] Dicionário de valores para a view.
	* @return mixed Objeto do tipo \Coupe\View ou nulo caso o arquivo de conteúdo não seja encontrado.
	* @see \Coupe::__getIdiomFolder()
	* @see \Coupe::config()
	* @see \Coupe\View
	*/
	public function template($viewid, Array $set=NULL)
	{
		
		$viewid = str_replace(array('/', '\\'), DS, $viewid); /* Trata o id da View. */
		
		$viewid = trim($viewid, DS);
		
		$paths = array(
			APP . 'templates' . DS . $this->__getIdiomFolder(),
			BASE . 'templates' . DS . $this->__getIdiomFolder(),
			APP . 'templates' . DS,
			BASE . 'templates' . DS
			);
		
		$plugins = $this->config()->get('plugins');
	
		foreach((array)$plugins as $plugin)
		{
			array_unshift($paths, BASE . 'plugins' . DS . $plugin . DS . 'templates' . DS . $this->__getIdiomFolder(), BASE . 'plugins' . DS . $plugin . DS . 'templates' . DS);
		}
		
		foreach( $paths as $path )
		{
			
			$view = new \Coupe\View\Template();

			$view->setTemplate($path . $viewid . '.php');

			if( is_file($path . $viewid . '.php') )
			{

				$view->set($set);	
			
				return $view;
			
			}
			
		}
		
		if( (substr_count($viewid, DS) + 1) >= 2 )
		{
		
			$rpos = strrpos($viewid, DS);
		
			$parent = substr($viewid, 0, $rpos);
			
			return $this->template($parent, $set);
		
		}
		elseif( $viewid!=$this->config()->get('template_default') )
		{

			return $this->template($this->config()->get('template_default'), $set);
		
		}
		
		return NULL;
		
	}

	/**
	* Dispara o carregamento dos recursos
	* solicitados através da URL.
	* 
	* @access public
	* @return string Resultado do carregamento e processamento de página.
	* @see \Coupe::__output()
	* @see \Coupe::_controller()
	* @see \Coupe::config()
	* @see \Coupe::controller()
	* @see \Coupe::dictionary()
	* @see \Coupe\Route
	* @see \Coupe\View
	* @see \Habilis\Mapper
	* @see \Habilis\Url
	*/
	public function dispatcher()
	{
		
		/* INICIAR DEBUG */

		/* Configurações */
		
		$this->config(array('datasource', 'settings', 'uri'));
		
		/* Dicionário */
		
		$this->dictionary('coupe');
		
		/* Autoload */
		
		$autoload = $this->config()->get('autoload');
		
		foreach(array('helper', 'controller', 'model', 'lib') as $type)
		{
			if( isset($autoload[ $type ]) && is_array($autoload[ $type ]) )
			{
				foreach($autoload[ $type ] as $toload)
				{
					$this->{$type}($toload);
				}
			}
		}
		
		/* Tratando as URLs */

		if( ! isset($_GET['p']) )
		{
			$_GET['p'] = '/';
		}
		
		$path = $_GET['p'];
		
		/* Mapas de rotas URLs */

		$this->_mapper = new \Habilis\Mapper($this->config()->get('mapper'));

		/* Rotas */

		$this->_route = new \Coupe\Route($path);

		$this->_route->setMapper($this->_mapper);

		$this->_route->setPrefix($this->config()->get('url_prefix'));
		
		/* Se o caminho é um arquivo em WEBROOT, retorna ele somente */
		
		$filepath = WEBROOT . trim($this->_route->get(), '/');
		
		$filepath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $filepath);
		
		if( is_file($filepath) )
		{
			
			$view = new \Coupe\View\Template();

			$view->setTemplate($filepath);
		
			return $this->__output($view);
			
		}

		/* Buscanco conteúdo */
		
		$this->_controller = $this->controller($this->_route->getController());
		
		if( $this->_controller )
		{
			
			$this->_controller->beforeAction();
		
			if( $this->_controller->callAction($this->_route->getAction(), $this->_route->getParameters()) )
			{
		
				if( $this->_controller->auto_render )
				{
					
					$this->_controller->beforeRender();

					/*
					 * Define o nome do arquivo de template.
					 */
					$viewid = ($this->_route->getAction()=='index') ? $this->_route->getController() : $this->_route->getController() . '/' . $this->_route->getAction();

					/*
					 * Se o atributo de view 'content'
					 * nao tiver sido definido no Controller
					 * tentar encontrar por um arquivo de view.
					 * Se tiver, nao faz nada e ele que será
					 * passado para a view de template.
					 */
					if( ! array_key_exists('content', $this->_controller->set) )
					{
						$this->_controller->set('content', $this->content($viewid, $this->_controller->set)); // $output = $this->content($viewid, $this->_controller->set, $template)->render();
					}

					/*
					 * Pega o template baseado no nome do
					 * arquivo e armazena o resultado do
					 * render em uma variavel que será
					 * usada como saída do programa.
					 */
					$output = $this->template($viewid, $this->_controller->set)->render(); /* Permitir que a template seja modificada por uma propriedade do objeto de controlle ou por algum parametro definido dentro das configurações. */


				}
		
				$this->_controller->afterFilter();

				return $this->__output($output);
		
			}
			
		}
		
		/* Se não achou o controller, tenta localizar um arquivo de View */
		
		$viewid = $this->_route->getView();

		$content = $this->content($viewid, NULL, $this->template($viewid));	

		if( $content )
		{

			return $this->__output($content->render());
		
		}
		elseif( $this->config()->get('url_complete') )
		{
			
			$filename = trim(str_replace(array('/', '\\'), DS, $viewid), DS) . '*';

			$paths = array(APP . 'contents' . DS . $this->__getIdiomFolder(), BASE . 'contents' . DS . $this->__getIdiomFolder());	
			
			foreach($paths as $p)
			{
				
				$temp = glob($path . DS . $filename);	
				
				if( $temp )
				{
					
					$newpath = str_replace(array($path . DS, '.php'), '', $temp[0]);
					
					$newurl = new \Habilis\Url(\Habilis\Url::current());
					
					$newurl->setQuery(array_merge($newurl->getQueryAsArray(), array('q'=>$newpath)));
					
					return $this->redirect($newurl, NULL, TRUE);
					
				}
				
			}
			
		}
		
		/* Se chegou aqui, é por que não encontrou nada. */

		return $this->error($this->dictionary()->get('error_404', $path), '404'); // return $this->error($this->dictionary()->line('error_404', $path), '404'); /* Se não encontrou uma saída até agora, retorna erro. */
	
	}

	/**
	* Dá saída no programa usando uma
	* das templates de erro do framework.
	* O tipo da template está
	* relacionado ao tipo de erro.
	*
	* @access public
	* @param string $output Mensagem de erro que será usada na template retornada.
	* @param string $type [optional] Tipo do erro. O tipo passado será usado pra carregar o arquivo template de erro de mesmo nome.
	* @param array $set [optional] Dicionários de conteúdos a serem usados na template.
	* @return void
	*/
	public function error($output, $type='unknow', Array $set=NULL)
	{		
		
		$this->log($output);
		
		$viewid = 'error\\' . (string)$type;
		
		(array)$set['content'] = $output;
	
		$template = $this->template($viewid, $set);

		if( $template )
		{
			$output = $template->render();
		}
	
		$this->__output($output);
		
	}
	
	/**
	* Grava a mensagem passada como
	* parametro nos arquivos de log
	* do framework. O segundo parâmetro
	* permite passar um prefixo para o
	* arquivo de log que será usado para
	* armazenar a mensagem. Dessa forma
	* é possível organizar os tipos
	* de mensagens a serem gravadas 
	* em arquivos de logs categorizados
	* por um prefixo.
	*
	* @access public
	* @param string $msg Mensagem a ser gravada em log.
	* @param string $prefix [optional] Prefixo do arquivo de log a ser utilizados.
	* @return mixed Verdadeiro se o arquivo foi gravado com sucesso ou nulo.
	* @see date()
	* @see \Habilis\Log
	* @see \Habilis\Log::write()
	*/
	public function log($msg, $prefix='')
	{
		
		if( $this->config()->get('active_log') )
		{
		
			$path = APP . 'tmp' . DS . 'log' . DS;
		
			$filename = $prefix . date("Y-m-d") . '.log';
		
			$filepath = $path . $filename;
		
			$log = new \Habilis\Log($filepath);

			$ip = getenv('REMOTE_ADDR');
		
			$log->write("[{$ip}] {$msg}");
			
			return TRUE;
		
		}
		
	}
	
	/**
	* Faz redirecionamento de página
	* para a URL passada através de um
	* objeto do tipo \Habilis\Url.
	* É possível passar o status do
	* redirecionamento (404, 303, etc.)
	* no segundo parâmetro.
	* O terceiro parametro indica se
	* o programa deve ser finalizado.
	*
	* @access public
	* @param \Habilis\Url $url Objeto com o caminho a ser redirecionado.
	* @param int $status [optional] Status do cabeçalho HTTP enviado.
	* @param bool $exit [optional] Encerrar aplicativo após redirecionamento.
	* @return void
	* @see header()
	*/
	public function redirect(\Habilis\Url $url, $status=NULL, $exit=TRUE)
	{
		
		$codes = array(
			100 => "Continue",
			101 => "Switching Protocols",
			200 => "OK",
			201 => "Created",
			202 => "Accepted",
			203 => "Non-Authoritative Information",
			204 => "No Content",
			205 => "Reset Content",
			206 => "Partial Content",
			300 => "Multiple Choices",
			301 => "Moved Permanently",
			302 => "Found",
			303 => "See Other",
			304 => "Not Modified",
			305 => "Use Proxy",
			307 => "Temporary Redirect",
			400 => "Bad Request",
			401 => "Unauthorized",
			402 => "Payment Required",
			403 => "Forbidden",
			404 => "Not Found",
			405 => "Method Not Allowed",
			406 => "Not Acceptable",
			407 => "Proxy Authentication Required",
			408 => "Request Time-out",
			409 => "Conflict",
			410 => "Gone",
			411 => "Length Required",
			412 => "Precondition Failed",
			413 => "Request Entity Too Large",
			414 => "Request-URI Too Large",
			415 => "Unsupported Media Type",
			416 => "Requested range not satisfiable",
			417 => "Expectation Failed",
			500 => "Internal Server Error",
			501 => "Not Implemented",
			502 => "Bad Gateway",
			503 => "Service Unavailable",
			504 => "Gateway Time-out"
		);
		
		if ( $status && isset($codes[ $status ]) )
		{
			header("HTTP/1.1 {$status} {$codes[ $status ]}");
		}
		
		header("Location: " . $url->asString());
		
		if ( $exit )
		{
			die();
		}
		
	}
	
	/**
	* Retorna a pasta de idioma usada.
	* A pasta de idioma é sempre igual a nulo
	* se o idioma atual for igual ao idioma padrão
	* definido nas configurações do framework.
	* Se o idioma atual for diferente do idioma
	* padrão, a pasta é igual ao código desse
	* idioma.
	*
	* @access private
	* @return mixed String do caminho para pasta com idioma o definido ou nulo.
	* @see \Coupe::config()
	* @see \Habilis\Idiom
	* @see \Habilis\Idiom::getInstance()
	*/
	private function __getIdiomFolder()
	{
		
		$current = \Habilis\Idiom::getInstance()->get();
		
		if( $current && $current!=$this->config()->get('idiom_default') )
		{
			return $current . DS;
		}
	
	}
	
	/**
	* Retorna a string passada como
	* parametro compactada, transformada
	* e cache. É usado para tratar o
	* resultado da execução do framework
	* permitindo uma resposta mais rápida
	* do servidor eliminando caracteres
	* redundantes.
	*
	* @access private
	* @param string $output String a ser tratada.
	* @return string String tratada.
	* @see preg_replace()
	*/
	private function __output($output)
	{
		
		if ( $this->config()->get('spaceless') ) /* Retira o excesso de espacos e quebra de linhas. */
		{
			$output = preg_replace('~\s\s+~', ' ', $output);
		}
		
		/* TERMINAR DEBUG */

		exit($output); /* VERIFICAR A POSSIBILIDADE DE COLOCAR MARCAÇÕES QUE REFERENCIAM FUNÇÕES E PODEM OU NÃO SEREM SALVAS EM CACHE [[]], [!!] */ /* VERIFICAR A RESOLUÇÃO DE TEXTTILE */
		
	}

}
