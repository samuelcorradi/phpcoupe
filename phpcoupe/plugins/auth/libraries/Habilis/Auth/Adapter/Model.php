<?php

namespace Habilis\Auth\Adapter;

include_once BASE . 'plugins/auth/libraries/Habilis/Auth/Adapter.php';

class Model implements \Habilis\Auth\Adapter
{
	
	/**
	 * Modelo de dados que será usado
	 * para fazer a consulta dos
	 * dados de acordo as informações
	 * de credencial.
	 * Geralmente passa-se um modelo
	 * de nome User.
	 *
	 * @var \Habilis\Model
	 */
	public $model;
	
	/**
	 * Recursão na busca por
	 * resultados na autenticação.
	 */
	public $recursion = 3;
	
	/**
	 * Método construtor do adapter
	 * que faz uso de modelos
	 * recebe o objeto do modelo que
	 * irá fazer a consulta de autenticação.
	 *
	 * @param \Habilis\Model $model
	 */
	public function __construct(\Habilis\Model & $model)
	{
		
		$this->model = $model;
		
	}

	/**
	 * Função para autenticar de acordo
	 * com as credenciais passadas.
	 *
	 * @return boolean Verdadeiro se encontrou registro.
	 * @throws Exception Falha ao recuperar dados.
	 */
	public function authenticate(Array $credentials=NULL)
	{

		$result = $this->model->getAll($credentials, NULL, $this->recursion, 1);

		if ( empty($result) )
		{
			return FALSE;
		}

		return TRUE;

	}

}

?>
