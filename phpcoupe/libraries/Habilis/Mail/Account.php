<?php
/**
 * Created by PhpStorm.
 * User: samuelcorradi
 * Date: 08/01/15
 * Time: 14:08
 */

namespace Habilis\Mail;


class Account
{

    /**
     * Endereço de email da conta.
     *
     * @var string
     */
    protected $_user;

    /**
     * Host do envio.
     *
     * @var string
     */
    protected $_host;

    /**
     * Nome.
     *
     * @var string
     */
    public $name;

    /**
     * Classe construtora.
     *
     * @param $user
     * @param $server
     * @param $name
     */
    public function __construct($user, $host, $name)
    {

        $this->setUser($user);

        $this->setHost($host);

        $this->setName($name);

    }

    public function setName($name)
    {

        $this->name = $name;

        return $this;

    }

    public function getName()
    {

        return $this->name;

    }

    public function setUser($user)
    {

        $this->_user = $user;

        return $this;

    }

    public function getUser()
    {

        return $this->_user;

    }

    public function setHost($host)
    {

        $this->_host = $host;

        return $this;

    }

    public function getHost()
    {

        return $this->_host;

    }

    /**
     * Gera o endereço de email a partir
     * do nome do usuário da conta e
     * o endereço.
     *
     * @return string
     */
    public function getEmailAddress()
    {

        return $this->getUser() . "@" . $this->getHost();

    }

}