<?php

namespace Habilis;

class Acl
{

	/**
	 * Constante que define o
	 * valor para bloqueio.
	 * Essa constante é usada
	 * para identificar se a
	 * regra deve ser bloqueada.
	 */
	const DENY = 0;

	/**
	 * Constante que define o
	 * valor para acesso.
	 * Essa constante é usada
	 * para identificar se a
	 * regra deve ser liberada.
	 */
	const ALLOW = 1;

	/**
	 * Caso não haja regras
	 * para um recurso, ele
	 * deverá ser bloqueado
	 * ou liberado.
	 * A abordagem padrão
	 * é liberar. Para aumentar
	 * o nível de segurança
	 * isso deve ser alterado
	 * para bloqueio atraves
	 * do método self::setDefaultAction().
	 *
	 * @see self::setDefaultAction()
	 * @var int
	 */
	protected $_default_permission = \Habilis\Acl::ALLOW;

	/**
	 * Define a ação padrão para
	 * uma regra que não existir.
	 * Assim, quando se busca
	 * uma regra e ela não está
	 * definida, diz qual deve
	 * ser sua resposta.
	 *
	 * @see self::getAction()
	 * @param $action int
	 * @return $this
	 * @throws \Exception
	 */
	public function setDefaultPermission($p)
	{

		if( in_array($p, array(self::ALLOW, self::DENY) ) )
		{
			$this->_default_permission = $p;
		}
		else
		{
			throw new \Exception("Unknown action.");
		}

		return $this;

	}

	/**
	 * Armazena regras de permissão
	 * de cada regra para cada
	 * recurso (suas ações possíveis)
	 * tanto para bloqueio quanto
	 * para permissão.
	 */
	protected $_permissions = array();

	/**
	 * Armazena permissão pai
	 * do objeto.
	 *
	 * @var Role
	 * @protected
	 */
	protected $_parents = array();

	/**
	 * Define uma regra pai para
	 * uma outra regra. Assim
	 * esta outra regra passa
	 * a incorporar as mesmas
	 * permissões de seu pai.
	 * Funciona como herança.
	 *
	 * @param $role
	 * @param null $parent
	 * @return $this
	 */
	public function setParent($role, $parent=NULL)
	{

		$this->_parents[ $role ] = $parent;

		return $this;

	}

	/**
	 * Dá permissão para uma regra fazer
	 * algo com algum recurso.
	 * O recurso e ação podem ser '*'
	 * (asterisco), indicando que
	 * se pode fazer qualquer coisa
	 * com qualquer recurso.
	 *
	 * @param $role
	 * @param string $resource
	 * @param string $action
	 * @return $this
	 */
	public function allow($role, $resource='*', $action='*')
	{
		
		$this->_permissions[ $resource ][ $role ][ $action ] = self::ALLOW;
		
		return $this;
		
	}
	
	/**
	* Tira permissão de uma regra fazer algo com algum recurso.
	* O recurso e ação pode ser '*' asterisco, indicando que
	* não se pode fazer nada com qualquer recurso.
	*/
	public function deny($role, $resource='*', $action='*')
	{
		
		$this->_permissions[ $resource ][ $role ][ $action ] = self::DENY;

		return $this;
		
	}
	
	/**
	 * Verifica a permissão
	 * de uma regra a um recurso.
	 *
	 * @param $role
	 * @param string $resource
	 * @param string $action
	 * @return bool
	 */
	public function isAllowed($role, $resource='*', $action='*')
	{

		return ($this->_getPermission($role, $resource, $action)== self::ALLOW) ? TRUE : FALSE;
		
	}

	/**
	 * Pega a permissão de uma
	 * regra para determinada ação
	 * sobre um recurso.
	 * Se ação não estiver definida
	 * retorna a permissão padrão.
	 *
	 * @param $role
	 * @param string $resource
	 * @param string $action
	 * @protected
	 * @return mixed
	 */
	protected function _getPermission($role, $resource='*', $action='*')
	{

		$is_allowed = self::_default_permission;

		if( isset($this->_permissions[ $resource ][ $role ][ $action]) )
		{
			$is_allowed = $this->_permissions[ $resource ][ $role ][ $action ];
		}
		elseif( array_key_exists($role, $this->_parents) )
		{
			$is_allowed = $this->getPermission($role, $this->_parents[ $role ], $action);
		}

		return  $is_allowed;

	}
	
}

?>
