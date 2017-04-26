<?php

namespace Habilis\Auth;

interface Storage
{
	
	/**
	 * Verifica que há informações
	 * armazenadas.
	 *
	 * @return bool Verdadeiro se há informações ou falso.
	 */
	public function isActive();
	
	/**
	 * Adiciona informação ao storage.
	 *
	 * @return $this
	 * @throws Exception Excessão caso falhe de salvar o dado.
	 */
	public function setData(Array $data);
	
	/**
	* Lê um dados da sessão.
	*
	* @return mixed Informação salva no storage.
	 * @throws Exception Excessão caso falhe recuperar o dado.
	*/
	public function getData();

	/**
	* Limpa a sessão. Usado em ações de logout.
	*
	* @return bool Verdadeiro informando que os dados foram removidos.
	*/
	public function clearData();
	
}

?>
