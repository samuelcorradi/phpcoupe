<?php

namespace Habilis\Auth;

	/**
	 * Class Adapter permite executar
	 * tarefas de autenticação em
	 * em serviços diferentes como
	 * SGBDs, flatfiles, etc.
	 *
	 * @package Habilis\Auth
	 */
interface Adapter
{

	/**
	 * Função para autenticar de acordo
	 * com as credenciais passadas.
	 *
	 * @return boolean Verdadeiro se encontrou registro.
	 * @throws Exception Falha ao recuperar dados.
	 */
	public function authenticate(Array $credentials=NULL);

}

?>
