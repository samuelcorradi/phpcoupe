<?php

namespace DataScribe;

abstract class Select
{
	
	/**
	* Armazena o datasource.
	*/
	protected $_adapter;

	/**
	* Armazena a lista de colunas usadas no Select.
	* O esquema é o seguinte:
	* $_column é um array onde cada posição deve conter um array.
	* Esses arrays, de cada posição, contém a tabela na primeira
	* posição e a lista de colunas na segunda posição.
	*/
	protected $_column = array();
	
	/**
	* Filtro.
	*/
	protected $_filter;

	/**
	* Lista de campos para ordenar.
	*/
	protected $_order;

	/**
	* Lista de campos para se agrupar.
	*/
	protected $_group;
	
	/**
	* Armazena as uniões de tabelas.
	*/
	protected $_join = array();
	
	/**
	* Tamanho do resultado.
	*/
	public $pagesize;

	/**
	* Página do resultado.
	*/
	public $pagenumber;
	
	/**
	* Cláusula distinct.
	*/
	public $distinct;
	
	/**
	* Construtor recebe uma tabela.
	*/
	public function __construct(\DataScribe\Adapter & $adapter, $table, Array $column=NULL, \DataScribe\Filter $filter=NULL)
	{
		
		$this->_adapter = $adapter;
		
		$this->setFrom($table, $column);
		
		if( $filter )
		{
			$this->setWhere($filter);
		}
		
	}
	
	public function __toString()
	{
		
		return $this->sql();
		
	}
	
	public function setFrom($table, Array $column=NULL)
	{

		$this->_column[] = array($table, $column);
		
		return $this;

	}

	/**
	* Filtro.
	*/
	public function setWhere(\DataScribe\Filter & $filter)
	{

		$this->_filter = $filter;

		return $this;

	}
	
	/**
	* Habilita DISTINCT na seleção.
	*/
	public function setDistinct()
	{

		$this->distinct = TRUE;

		return $this;

	}
	
	/**
	* Faz a paginação.
	*/
	public function setPage($size, $number=0)
	{
		
		$this->_pagesize = (int)$size;
		
		$this->_pagenumber = (int)$number;
		
		return $this;
		
	}

	public function setGroup($field)
	{
		
		$this->_group[] = $field;
		
		return $this;

	}

	public function setOrder($field, $direction='asc')
	{

		$this->_order[ $field ] = $direction;

		return $this;

	}

	public function setJoin($table, \DataScribe\Filter & $filter, $type=NULL)
	{

		$this->_join[] = array($table, $filter, $type);
		
		return $this;

	}
	
	/**
	* Executa a seleção usando o adaptador.
	*/
	public function run()
	{
		
		return $this->_adapter->query($this->sql());
	
	}
	
	/**
	* Todo objeto que retorna SQL deve ter um método SQL.
	* 
	* @param $sql string SQL a ser executado.
	*/
	public abstract function sql();

}

?>
