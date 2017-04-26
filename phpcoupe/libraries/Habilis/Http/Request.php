<?php

namespace Habilis\Http;

use \Habilis\Http\Response as Response;

/*
* Classe provê uma interface fácil
* para envio de requisições HTTP.
* Suporta instruções simples suportadas
* pelo PHP, bem como envio de arquivos
* e autenticação.
* Classe que manipula os dados
* de requisição do cliente.
* Como não tem acesso a recursos
* profundos de um componente
* HTTP vinculado ao servidor web,
* confia basicamente nos dados
* disponibilizados através do
* array global $_SERVER.
*/
class Request extends \Habilis\Http 
{

	/**
	* 
	*/
	const METHOD_GET = 'GET';

	/**
	* 
	*/
	const METHOD_POST = 'POST';

	/**
	* 
	*/
	const METHOD_PUT = 'PUT';

	/**
	* 
	*/
	const METHOD_DELETE = 'DELETE';

	/**
	* Método para efetuar a requisição.
	* Padrão é GET.
	*
	* @var string
	*/
	protected $_method = self::METHOD_GET;

	/**
	* URI para onde deverá ser
	* feita a requisição.
	*
	* @var mixed
	*/
	protected $_host;

	/**
	* Dados usados em requisições usando
	* o método GET.
	*
	* @var array
	*/
	protected $_get_data;

	/**
	* Dados usados em requisições usando
	* o método POST.
	*
	* @var array
	*/
	protected $_post_data;

	//TODO: não seria interessante usar o objeto URL aqui?
	/*
	* Recebe como parâmetro a URI para
	* o qual fará a requisição.
	*/
	public function __construct($host = "http://127.0.0.1", $method=NULL, $path=NULL)
	{

		parent::__construct();

		$this->setHost($host);

	}

	/**
	* Define os dados a serem submetidos
	* com o método GET.
	*/
	public function setGetData(Array $d)
	{

		$this->_get_data = $d;

		return $this;

	}

	/**
	* Define os dados a serem submetidos
	* com o método POST.
	*/
	public function setPostData(Array $d)
	{

		$this->_post_data = $d;

		return $this;

	}

	/**
	* É uma requisição com método OPTIONS?
	*
	* @return bool
	*/
	public function isOptions()
	{

		return ($this->_method === self::METHOD_OPTIONS);

	}

	/**
	* É uma requisição com método PROPFIND?
	*
	* @return bool
	*/
	public function isPropFind()
	{

		return ($this->_method === self::METHOD_PROPFIND);

	}

	/**
	* É uma requisição com método GET?
	*
	* @return bool
	*/
	public function isGet()
	{

		return ($this->method === self::METHOD_GET);

	}

	/**
	* É uma requisição com método HEAD?
	*
	* @return bool
	*/
	public function isHead()
	{

		return ($this->method === self::METHOD_HEAD);

	}

	/**
	* É uma requisição com método POST?
	*
	* @return bool
	*/
	public function isPost()
	{

		return ($this->method === self::METHOD_POST);

	}

	/**
	* É uma requisição com método PUT?
	*
	* @return bool
	*/
	public function isPut()
	{

		return ($this->method === self::METHOD_PUT);

	}

	/**
	* É uma requisição com método DELETE?
	*
	* @return bool
	*/
	public function isDelete()
	{

		return ($this->method === self::METHOD_DELETE);

	}

	/**
	* É uma requisição com método TRACE?
	*
	* @return bool
	*/
	public function isTrace()
	{

		return ($this->method === self::METHOD_TRACE);

	}

	/**
	* É uma requisição com método CONNECT?
	*
	* @return bool
	*/
	public function isConnect()
	{

		return ($this->method === self::METHOD_CONNECT);

	}

	/**
	* É uma requisição com método PATCH?
	*
	* @return bool
	*/
	public function isPatch()
	{

		return ($this->method === self::METHOD_PATCH);

	}

	/**
	* É uma requisição Javascript XMLHttpRequest?
	*
	* @return bool
	*/
	public function isXmlHttpRequest()
	{

	}

	/**
	* É uma requisição de Flash? (por que alguém usaria isso?)
	*
	* @return bool
	*/
	public function isFlashRequest()
	{

	}

	/**
	* Define a URI para onde a requisição
	* será feita.
	*
	* @param mixed $uri Uma URI ou objeto \Habilis\Uri
	* @return self
	*/
	public function setHost($host)
	{

		$this->_host = (string)$host;
		
	}

	/**
	* Pega a URI para onde é feita a requisição.
	*
	* @return string Caminho para o servidor.
	*/
	public function getHost()
	{

		return $this->_host;
		
	}

	/*
	* Define o método.
	*/
	public function setMethod($m)
	{

		$m = strtoupper($m);

		if ( ! defined('static::METHOD_' . $m) )
		{
			throw new Exception('Invalid HTTP method passed');
		}

		$this->method = $m;

		return $this;

	}

	/*
	* Pega o método usado.
	*/
	public function getMethod()
	{

		return $this->_method;

	}

	/**
	* Define a autenticação via HTTP.
	*/
	public function setAuth($user, $pass, $mode=\Habilis\Http\Header::AUTH_BASIC)
	{

		/*
		* Atenticação no modo básico.
		*/
		if($mode===\Habilis\Http\Header::AUTH_BASIC)
		{

			if ( strpos($user, ':') !== FALSE )
			{
				throw new Exception("The user name cannot contain ':' in 'Basic' HTTP authentication");
			}

			$this->getHeader()->add('Authorization', 'Basic ' . base64_encode($user . ':' . $pass));

		}
		/*
		* Atenticação no modo digest.
		*/
		elseif($mode===\Habilis\Http\Header::AUTH_DIGEST)
		{

		}

	}

	/**
	* Define o Cookie a ser enviado.
	*/
	public function setCookie($c)
	{

		$this->getHeader()->add('Cookie', $c, '=', '; ');

		return $this;

	}

	/**
	* Define o charset.
	*/
	public function charset($c)
	{

		$this->getHeader()->add('Accept-Charset', $c, ';q=');

		return $this;

	}

	/**
	* Define qual codificação o cliente aceita.
	* Dessa forma o servidor pode utilizar
	* algum método de compactação de conteúdo,
	* e assim economizar banda de rede.
	*/
	public function encoding($code)
	{

		$this->_Header->add('Accept-Encoding', $code, ';q=');

		return $this;

	}

	/**
	* Define os idiomas preferidos pelo
	* cliente para negociação com o
	* servidor.
	*/
	public function language($l)
	{

		$this->_Header->add('Accept-Language', $a, ';q=');

		return $this;

	}

	/**
	* Define os formatos de documento
	* aceitos pelo cliente. A reposta
	* do servidor deverá corresponder
	* a esse formato.
	*/
	public function accept($a)
	{

		$this->_Header->add('Accept', $a, ';q=');

		return $this;

	}

	// TODO: A forma de disparo da requisição pode ser feita de N formas diferentes. Por isso implementar drivers para isso.
	/**
	 * Dispara a requisição para o servidor.
	 */
	public function request()
	{

		$opt = array('http' => array(
		'max_redirects' => $this->max_redirect,
		'method'  => $this->_method,
		// 'user_agent'  => $this->agent,
		'header'  => ($this->_Header) ? $this->_Header->toString() : '',
		'content' => $this->_body
		));

		$ctx = stream_context_create($opt);

		$h = fopen($this->_host, 'r', FALSE, $ctx);

		$resp = implode("\r\n", $http_response_header) . "\r\n\r\n" . stream_get_contents($h);

		fclose($h);

		return Response::fromString($resp);

	}

	/**
	*
	*/
	public function renderRequestLine()
	{

		$s = sprintf(
		'%s %s HTTP/%s',
		$this->getMethod(),
		$this->getHost(),
		$this->getVersion()
		);

		return trim($s);

	}

	/**
	* Imprime o objeto em formato
	* de string de modo que revele
	* o código do cabeçalho e corpo.
	*/
	public function toString()
	{

		return $this->renderRequestLine() . "\r\n" . $this->_Header->toString() . "\r\n" . $this->getBody();

	}

	/**
	* Cria uma objeto do tipo Request através
	* de uma string que represente uma requisição
	* HTTP.
	* $req = Request::fromString(<<<EOS
	* GET 127.0.0.1 HTTP/1.0
	* HeaderField1: header-field-value
	* HeaderField2: header-field-value2
	* <html>
	* <body>
	*	Hello World
	* </body>
	* </html>
	* EOS);
	*
	* @return \Habilis\Http\Request
	*/
	public static function fromString($s)
	{

	}

}

?>