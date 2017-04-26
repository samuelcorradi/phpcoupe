<?php

namespace DataScribe;

abstract class Table
{

	/*
	* --------------------- Trazidas da classe model.
	*/

	/**
	* Caso esteja definido indica que
	* a validação de campos deve ser
	* feita a cada salvamento ou
	* alteração de dados.
	* @access public
	* @var bool
	*/
	public $validate = FALSE;

	/*
	* --------------------- Trazidas da classe model.
	*/
	
	/**
	* Cria uma tabela no banco de dados.
	*/
	public static abstract function create(\DataScribe\Table & $table);
	
	/**
	* Referência para o datasource do banco.
	*/
	protected $_adapter;
	
	/**
	* Nome do banco.
	*/
	protected $_name;
	
	/**
	* Prefixo da tabela.
	*/
	public $prefix;
	
	/**
	* Construtor da tabela.
	*/
	public function __construct(\DataScribe\Adapter & $adapter, $name)
	{
	
		$this->_adapter = $adapter;
	
		$this->_name = $name;
	
	}
	
	/**
	* Retorna o datasource da tabela.
	*/
	public function getAdpter()
	{
	
		return $this->_adapter;
	
	}

	/**
	* Retorna o nome da tabela.
	*/
	public function getName()
	{

		return $this->_name;

	}

	/**
	* Retorna o nome do esquema da tabela.
	* Em Oracle se chama Schema, em MySQL é o Banco
	*/
	public function getSchema()
	{

		return $this->_adapter->getConfig('schema');

	}

	/**
	* Retorna a lista de campos da tabela.
	*/
	public function fieldList()
	{

		$info = $this->info();

		$list = array();

		foreach( $info as $k => $v )
		{
			$list[] = $k;
		}

		return $list;

	}

	/**
	* Indica se um campo existe ou não na tabela.
	*/
	public function fieldExists($field_name)
	{
		
		return in_array($field_name, $this->fieldList());
		
	}

	public function insert(Array $data)
	{

		$sql = 'INSERT INTO '. $this->_name;

		$fields = array();
	
		$values = array();

		foreach ($data as $k => $v)
		{

			$fields[] = "`{$k}`";

			$values[] = $v;

		}

		$sql .= ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ');';

		return $this->_adapter->query($sql);

	}


	public function update(Array $data, Array $filter=NULL)
	{
		
		$sql = 'UPDATE ' . $this->_name . ' SET ';
		
		$fields = array();

		foreach ($data as $k => $v)
		{
			$fields[] = $k . '=' . $v;
		}

		$sql .= implode(', ', $fields);

		if( $filter )
		{
			$sql .= ' WHERE ' . $this->_adapter->filter($filter)->sql();
		}

		return $this->_adapter->query($sql);
		
	}
	
	public function delete(Array $filter=NULL)
	{

		$sql = "DELETE FROM " . $this->_name;

		if( $filter )
		{
			$sql .= ' WHERE ' . $this->_adapter->filter($filter)->sql();
		}

		return $this->_adapter->query($sql);

	}
	
	/**
	* Pega o esquema de dados da tabela.
	*/
	abstract public function info();
	
	/**
	* Cria um índice.
	*/
	abstract public function addIndex($name, Array $attr=NULL);
	
	/**
	* Remove um índice.
	*/
	abstract public function dropIndex($name);
	
	/**
	* Cria um campo na tabela.
	*/
	abstract public function addField($name, Array $attr=NULL);

	/**
	* Altera um campo da tabela.
	*/
	abstract public function changeField($name, Array $attr=NULL);
	
	/**
	* Remove um campo da tabela.
	*/
	abstract public function dropField($name, Array $attr=NULL);
	
}

?>
