<?php

namespace DataScribe;

abstract class Result
{

	protected $_adapter;
	
	protected $_handler;

	protected $_query;

	/**
	* Determina se as operações são transacionais.
	*/
	public $transactional;
	
	public function __construct($query, \Habilis\Db\Adapter & $adapter)
	{
		
		$this->_query = $query;
		
		$this->_adapter = $adapter;
		
		$this->query();

	}
	
	/**
	* Retorna o objeto do banco.
	*/
	public function getAdapter()
	{
		
		return $this->_adapter;
		
	}
	
	public function getQuery()
	{
		
		return $this->_query;
	
	}
	
	public function __toString()
	{
		
		return $this->_query;
		
	}

	/**
	* Executa uma query em banco de retorna
	* o ponteiro para o resultado.
	*/
	public function query()
	{

		$this->_handler = $this->_query();
		
		return $this->_handler;
		
	}
	
	public function getHandler()
	{
	
		return $this->_handler;
	
	}

	/**
	* Inicia uma transação. Retorna verdadeiro
	* caso não seja implementado no SGBD.
	*
	* @access public
	*/
	public function begin()
	{
		
		return TRUE;
		
	}

	/**
	* Comita uma transação. Retorna verdadeiro
	* caso não seja implementado no SGBD.
	*
	* @access public
	*/
	public function commit()
	{
		
		return TRUE;
		
	}

	/**
	* Desfaz uma transação. Retorna verdadeiro
	* caso não seja implementado no SGBD.
	*
	* @access public
	*/
	public function rollback()
	{
		
		return TRUE;
		
	}
	
	/*
	* Retorna a quantidade de resultados
	* encontrados na ultima consulta.
	*/
	public function count()
	{
		
		if( $this->_handler )
		{
			return $this->_count();
		}
		else
		{
			return NULL;
		}
	
	}

	/**
	* Reinicia o ponteiro para resultados no banco.
	*/
	public function dataSeek($pos=0)
	{

		return $this->_dataSeek($pos);

	}
	
	public function fetch()
	{

		if( $this->_handler )
		{
	
			$result = $this->_fetch();

			$this->dataSeek(0); // depois de dah um fetch, usa essa funcao para retornar o ponteiro para o inicio
	
			return $result;

		}

	}
	
	public function affectedRows()
	{

		return $this->_affectedRows();

	}
	
	public function lastId()
	{

		return $this->_lastId();

	}
	
	/* Métodos especializados. */
	
	abstract protected function _query();

	abstract protected function _count();
	
	abstract protected function _dataSeek($pos);
	
	abstract protected function _fetch();
	
	abstract protected function _affectedRows();

	abstract protected function _lastId();
	
}

?>
