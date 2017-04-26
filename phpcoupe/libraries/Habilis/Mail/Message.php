<?php
/**
 * Created by PhpStorm.
 * User: samuelcorradi
 * Date: 08/01/15
 * Time: 14:09
 */

namespace Habilis\Mail;


class Message
{

    /*
     * Definições de prioridade.
     */

    const PRIORITY_HIGHEST = 1;

    const PRIORITY_HIGH = 2;

    const PRIORITY_NORMAL = 3;

    const PRIORITY_LOW = 4;

    const PRIORITY_LOWEST = 5;

    protected $_priority = 3;

    /*
     * Definições de formatos.
     */

    const FORMAT_PLAIN = 'plain';

    const FORMAT_HTML = 'html';

    protected $_format = 'plain';

    /**
     * Título padrão das mensagens.
     */
    const DEFAULT_SUBJECT = "(Untitled)";

    /**
     * @var string
     */
    public $subject = self::DEFAULT_SUBJECT;

    /**
     * @var string
     */
    public $charset = "iso-8859-1";

    /*
     * Lista de emails.
     */

    /**
     * Armazena a lista de destinatários.
     *
     * @access protected
     * @var array
     */
    protected $_to = array();

    /**
     * Armazena a lista de endereços
     * que serão enviados cópias.
     *
     * @access protected
     * @var array
     */
    protected $_cc = array();

    /**
     * Armazena a lista de endereços
     * que serão enviados cópias ocultas.
     *
     * @access protected
     * @var array
     */
    protected $_bcc = array();

    /**
     * Texto da mensagem.
     *
     * @var string
     */
    protected $_text = '';

    /**
     * Objeto de cabeçalho HTTP utilizado
     * no envio.
     *
     * @var
     */
    protected $_Header;

    /**
     * Conta que será usada para identificar
     * a mensagem.
     *
     * @var \Habilis\Mail\Account
     */
    protected $_Account;

    /**
     * Método construtor instacia um objeto
     * do tipo \Habilis\Http\Header\Request
     * que será usado no envio da mensagem.
     *
     * @param Account $a
     */
    public function __construct(\Habilis\Mail\Account $a)
    {

        $this->_Account = $a;

        $this->_Header = new \Habilis\Http\Header\Request();

    }

    /*
     * Conta.
     */

    /**
     * Define uma conta para identificar a mensagem.
     *
     * @param Account $a
     * @return $this
     */
    public function setAccount(\Habilis\Mail\Account $a)
    {

        $this->_Account = $a;

        return $this;

    }

    /**
     * Pega a conta definida para identificação
     * da mensagem.
     *
     * @return \Habilis\Mail\Account
     */
    public function getAccount()
    {

        return $this->_Account;

    }

    /*
     * Título.
     */

    /**
     * Define um título para a mensagem.
     *
     * @param $subject
     * @return $this
     */
    public function setSubject($subject)
    {

        $this->subject = $subject;

        return $this;

    }

    /**
     * Retorna o título da mensagem.
     *
     * @return string
     */
    public function getSubject()
    {

        return $this->subject;

    }

    /*
     * Prioridade.
     */

    /**
     * Pega a prioridade indicada
     * para a mensagem.
     *
     * @return string
     */
    public function getPriority()
    {

        return $this->_priority;

    }

    /**
     * Define uma prioridade para a mensagem.
     *
     * @param $p
     * @return $this
     * @throws \Exception
     */
    public function setPriority($p)
    {

        $const_name = "PRIORITY_" . strtoupper($p);

        if( isset(self::$const_name) )
        {

            $this->_priority = self::$const_name;

            return $this;

        }

        throw new \Exception("Invalid priority format.");

    }

    /*
     * Formato.
     */

    /**
     * @return string
     */
    public function getFormat()
    {

        return $this->_format;

    }

    /**
     * @param $type
     * @return $this
     * @throws \Exception
     */
    public function setFormat($type)
    {

        $const_name = "FORMAT_" . strtoupper($type);

        if( isset(self::$const_name) )
        {

            $this->_format = self::$const_name;

            return $this;

        }

        throw new \Exception("Invalid message format.");

    }

    /*
     * Endereçamento.
     */

    /**
     * @param string $email
     * @param string $name
     * @return $this
     * @throws \Exception
     */
    public function to($email, $name='')
    {

        if( filter_var($email, FILTER_VALIDATE_EMAIL) )
        {

            $this->_to[ $email ] = $name;

            return $this;

        }

        throw new \Exception("Invalid e-mail address format.");

    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     * @throws \Exception
     */
    public function cc($email, $name='')
    {

        if( filter_var($email, FILTER_VALIDATE_EMAIL) )
        {

            $this->_cc[ $email ] = $name;

            return $this;

        }

        throw new \Exception("Invalid e-mail address format.");

    }

    /**
     * @param string $email
     * @param string $name
     * @return $this
     * @throws \Exception
     */
    public function bcc($email, $name='')
    {

        if( filter_var($email, FILTER_VALIDATE_EMAIL) )
        {

            $this->_bcc[ $email ] = $name;

            return $this;

        }

        throw new \Exception("Invalid e-mail address format.");

    }

    /*
     * Texto da mensagem.
     */

    /**
     * Retorna o texto da mensagem.
     *
     * @return string
     */
    public function getText()
    {

        return $this->_text;

    }

    /**
     * Define um texto para a mensagem.
     *
     * @param $msg
     * @return $this
     */
    public function setText($msg)
    {

        $this->_text = stripslashes( rtrim( str_replace("\r", "", (string)$msg) ) );

        return $this;

    }

    /*
     * Header.
     */

    /**
     * Retorna o objeto de cabeçalho.
     *
     * @return \Habilis\Http\Header\Request
     */
    public function getHeader()
    {

        return $this->_Header;

    }

    /**
     * Gera o formato de endereço de email usado
     * nos campos de destinatário.
     * Se email e nome forem informado, retorna
     * no formato "Nome <email>".
     * Um array pode ser passado como parametro
     * e suas chaves serão consideradas os
     * emails e os valores os nomes.
     * Fazendo recursividade retorna uma lista
     * de emails separados por ',' (virgula),
     * no formato padrão de lista de endereços
     * para envio em programas de email.
     * "Nome <email>, Outro Nome <outro_email>".
     *
     * @param $email
     * @param null $name
     * @return string
     */
    final protected static function renderEmailAddress($email, $name=NULL)
    {

        if (is_array($email) )
        {

            $mlist = array();

            foreach ($mlist as $m => $n)
            {
                $mlist[] = self::renderEmailAddress($m, $n);
            }

            return implode(', ', $mlist);

        }

        return ( ! strlen((string)$name) ) ? $email : "$name <{$email}>";

    }

    /**
     * Faz a configuração do cabeçalho HTTP
     * com as informações do email e utiliza
     * a função mail() do PHP para envio
     * da mensagem.
     *
     * @return bool
     */
    public function send()
    {

        /*
         * Define o MIME.
         */
        $this->_Header->set('MIME-Version', "1.0");

        /*
         * Define o formato da mensagem.
         */
        switch($this->_format)
        {

            case self::FORMAT_HTML :

                $this->_Header->content_type = "text/html; charset={$this->charset}";

                break;

            case self::FORMAT_PLAIN :

                $this->_Header->content_type = "text/plain; charset={$this->charset}";

                break;

        }

        $this->_Header->replay_to = self::renderEmailAddress($this->_Account->getEmailAddress(), $this->_Account->getName());

        $this->_Header->x_mailer = "PHP/" . phpversion();

        $this->_Header->subject = $this->getSubject();

        $this->_Header->from = self::renderEmailAddress($this->_Account->getEmailAddress(), $this->_Account->getName());

        if( ! empty($this->_cc) )
        {
            $this->_Header->cc = self::renderEmailAddress($this->_cc);
        }

        if( ! empty($this->_bcc) )
        {
            $this->_Header->bcc = self::renderEmailAddress($this->_bcc);
        }

        return mail(self::renderEmailAddress($this->_to), $this->getSubject(), wordwrap($this->_text, 70), $this->_Header);

    }

}