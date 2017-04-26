<?php

namespace Habilis\Http;

/*
* Resposde a requisições HTTP.
* Método que trada que elaborar
* as respostas a serem geradas
* pelo servidor. Recebe o objeto
* de requisição do cliente para
* elaborar suas respostas.
* Muito útil para negociação
* de conteúdo.
*/
class Response extends \Habilis\Http 
{

	// CONSTANTES

	const STATUS_CODE_200 = 200;

	const STATUS_CODE_404 = 404;

	// PROTEGIDAS

	/**
	* Status da resposta enviada.
	*
	* @var int Código do status.
	*/
	protected $_status_code = 200;

	/**
	* Corpo da resposta a ser enviada.
	*
	* @var string Frase customizada.
	*/
	protected $_body;

	/**
	* Frase customizada a ser adiciona a
	* resposta.
	*
	* @var string Frase customizada.
	*/
	protected $_custom_phrase;

	/**
	* Configurações de frases que são
	* adicionados ao cabeçalho da resposta
	* de acordo com o status da mesma.
	*
	* @var array Frases associadas ao seu status.
	*/
	protected $_status_phrases = array(
	200 => 'OK',
	404 => 'Not Found',
	);

	// PRIVADAS

	/*
	* Armazena o objeto de requisiçao.
	*/
	private $__req;

	/*
	* A resposta HTTP será elaborada
	* através dos dados provenientes
	* da requisição.
	*/
	// public function __construct(\Habilis\Http\Request $req)
	// public function __construct($code, \Habilis\Http\Header $h=NULL, $b=NULL, $version='1.1', $msg=NULL)
	// {

	// 	$this->setStatusCode($code);

	// 	$this->setHeader($h);

	// 	$this->Header->setHeader($headers);

	// 	$this->setBody($b);

	// 	$this->setCustomPhrase($msg);
	
	// }

	/**
	* Define o status da resposta.
	*/
	public function setStatusCode($code)
	{

		$const = get_class($this) . '::STATUS_CODE_' . $code;

		if ( ! is_numeric($code) || ! defined($const) )
		{
			throw new \Exception("Um valor do tipo numérico deve ser informado.");
		}

		$this->_status_code = (int) $code;

		return $this;

		}

	/**
	* Pega o status code.
	*/
	public function getStatusCode()
	{

		return $this->_status_code;

	}

    /**
    * Define uma frase customizada que será
    * usada no cabeçalho da resposta independente
    * do status da mesma.
    */
	public function setCustomPhrase($p)
	{

		$this->_custom_phrase = trim($p);

		return $this;

	}

	/**
	*
	*/
	public function renderStatusLine()
	{

		$s = sprintf(
		'HTTP/%s %d %s',
		$this->getVersion(),
		$this->getStatusCode(),
		$this->getStatusPhrase()
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

		return $this->renderStatusLine() . "\r\n" . $this->_Header->toString() . "\r\n" . $this->getBody();

	}

	/**
    * Pega a frase de status que faz parte
    * do cabeçalho de resposta.
    */
    public function getStatusPhrase()
    {
    
        if ( empty($this->_custom_phrase) && isset($this->_status_phrases[ $this->status_code ]) )
        {
            return $this->_status_phrases[ $this->status_code ];
        }

        return $this->_custom_phrase;
    
    }

	/*
	* Através da negociação de formatos
	* usando os formatos suportados
	* e os formatos esperados pelo
	* cliente se decide que formato
	* usar.
	*/
	public function getFormat()
	{

		foreach($this->__req->getAccepted() as $f)
		{
			if( in_array($f, $this->supported_formats) )
			{
				break;
			}
		}

		return $f;

	}

	/**
	* O status code significa um erro do cliente?
	*
	* @return bool
	*/
	public function isClientError()
	{

		$c = $this->getStatusCode();

		return ($c<500 && $c>=400);

	}

	/**
	* 
	* @return bool
	*/
	public function isForbidden()
	{

		return (403===$this->getStatusCode());

	}

	/**
	* @return bool
	*/
	public function isInformational()
	{

		$c = $this->getStatusCode();

		return ($c>=100 && $c<200);

	}

	/**
	* Status indica que o conteúdo não
	* foi encontrado?
	*
	* @return bool
	*/
	public function isNotFound()
	{

		return (404===$this->getStatusCode());

	}

	/**
	* Status indica uma resposta normal?
	*
	* @return bool
	*/
	public function isOk()
	{

		return (200===$this->getStatusCode());

	}

	/**
	* Status indica um erro no servidor?
	*
	* @return bool
	*/
	public function isServerError()
	{

		$c = $this->getStatusCode();

		return (500<=$c && 600>$c);

	}

	/**
	* É um redirecionamento?
	*
	* @return bool
	*/
	public function isRedirect()
	{

		$c = $this->getStatusCode();

		return (300<=$c && 400>$c);

	}

	/**
	*
	* @return bool
	*/
	public function isSuccess()
	{

		$c = $this->getStatusCode();

		return (200 <= $c && 300 > $c);

	}

	public function response()
	{
		
	}

	/**
	* Cria um objeto de requisição baseado
	* no array global $_SERVER.
	*/
	public static function fromServerVar()
	{

		$resp = new self();

	}

	/**
	* Cria uma objeto do tipo Response através
	* de uma string que represente uma resposta
	* HTTP.
	* $response = Response::fromString(<<<EOS
	* HTTP/1.0 200 OK
	* HeaderField1: header-field-value
	* HeaderField2: header-field-value2
	* <html>
	* <body>
	*	Hello World
	* </body>
	* </html>
	* EOS);
	*
	* @return \Habilis\Http\Response
	*/
	public static function fromString($s)
	{

		$l = explode("\r\n", $s);

		if ( ! is_array($l) || count($l)==1)
		{
			$l = explode("\n", $s);
		}

		$firstl = array_shift($l);

		$resp = new static();

		$m = array();

		if (!preg_match('/^HTTP\/(?P<version>1\.[01]) (?P<status>\d{3})(?:[ ]+(?P<reason>.*))?$/', $firstl, $m))
		{
			throw new Exception('A valid response status line was not found in the provided string');
		}

		$resp->setVersion(preg_replace('/[^0-9]+/', '', $m['version']));

		try
		{
			$resp->setStatusCode($m['status']);
		}
		catch(\Exception $e)
		{
			throw new \Exception($e->getMessage(), 1);
		}

		$resp->setCustomPhrase((isset($m['reason']) ? $m['reason'] : ''));

		if (count($l)==0)
		{
			return $resp;
		}

		$body = array();

		$is_header = TRUE;

		foreach ($l as $line)
		{

			if ($is_header && $line=="")
			{
				$is_header = FALSE; continue;
			}

			if ($is_header)
			{
				$resp->getHeader()->add($line); // $headers[] = $line;
			}
			else
			{
				$body[] = $line;
			}

		}

		if ( ! empty($body) )
		{
			$resp->setBody(implode("\r\n", $body));
		}

		return $resp;

	}

}

?>