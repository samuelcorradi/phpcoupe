<?php

namespace Coupe\Model;

abstract class Mysql extends \Coupe\Model
{

	/**
	* Parâmetros de conexão.
	*/
	protected $_dbsettings = array(
		'host'=>'127.0.0.1',
		'user'=>'root',
		'pass'=>'Paranoic007',
		'name'=>'habilis',
		);
	
	/**
	* Armazena o ponteiro de conexão.
	*/
	protected $_conn;

	/**
	* Formato de hora geralmente encontrado nos bancos de dados.
	*/
	public $dateformat = 'Y-m-d H:i:s';

	protected function __construct()
	{	

		$this->_conn = mysql_connect($this->_dbsettings['host'], $this->_dbsettings['user'], $this->_dbsettings['pass']);

		if( ! $this->_conn )
		{
			trigger_error('Could not connect.', E_USER_ERROR);
		}
	
		if( ! mysql_select_db($this->_dbsettings['name']) )
		{
			trigger_error('Failed to select database.', E_USER_ERROR);
		}
	
		parent::__construct();
	
	}

	/**
	* Atualiza o registro com o id fornecido e retorna o id.
	* @param array $data Dados a serem gravados.
	* @param array|NULL $filter [optional] Filtro que seleciona as linhas a serem atualizadas.
	* @return int Quantidade de linhas atualizadas ou falso.
	*/
	public function update(Array $data, Array $filter=NULL)
	{
	
		$sql = 'UPDATE ' . $this->stagename . ' SET ';
	
		foreach($data as $k => $v)
		{
			$tmp[] = "{$k}=" . $this->__parseValue($v);
		}
	
		$sql .= implode(', ', $tmp);
	
		if( $filter )
		{
			$sql .= ' WHERE ' . $this->__where($filter);
		}
	
		$result = $this->_query($sql);
	
		if( $result )
		{
			return mysql_affected_rows($this->_conn);
		}

		return 0;
	
	}


	/**
	* Insere os dados não persistidos
	* no stage de armazenamento
	* (caso use AUTO_INCREMENT).
	* @param array $data Array associativo onde chave indica o campo.
	* @return int Valor da chave primária do registor inserido.
	*/
	public function insert(Array $data)
	{
	
		foreach($data as $k => $v)
		{
		
			$fields[] = $k;
		
			$values[] = $v;
		
		}
	
		$sql = 'INSERT INTO ' . $this->stagename . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ');';
	
		$result = $this->_query($sql);

		if( $result )
		{
		
			$this->id = mysql_insert_id($this->_conn);
		
			return $this->id;
	
		}

	}

	/**
	* Remove todas as ocorrências do
	* stage ou apenas as indicadas
	* por um filtro.
	*
	* @abstract
	* @access protected
	* @param array $filter [optional] Filtro para remoção dos dados.
	* @return bool Verdadeiro casos os dados sejam removidos.
	*/
	protected function _deleteAll(Array $filter=NULL)
	{
	
		$sql = 'DELETE FROM ' . $this->stagename;
	
		if( $filter )
		{
			$sql .= ' WHERE ' . $this->__where($filter);
		}
	
		$result = $this->_query($sql);
	
		if( $result )
		{
			return mysql_affected_rows($this->_conn);
		}
	
	}

	/**
	* Pega a lista de valores ÚNICOS de determinado
	* campo. Pode-se passar um filtro para restringir
	* a consulta.
	*/
	public function getFieldValues($field=NULL, Array $filter=NULL)
	{

		if ( ! $field )
		{
			$field = $this->primary();
		}
	
		$sql = "SELECT DISTINCT `" . $field . "` FROM " . $this->stagename;
	
		if( $filter )
		{
			$sql .= ' WHERE ' . $this->__where($filter);
		}
	
		$result = $this->_query($sql);
	
		if( $result )
		{
	
			$table = array();
		
			while( $row = mysql_fetch_row($result) )
			{
				$table[] = $row[0];
			}
		
			return $table;
	
		}

	}

	// ok
	// le um resultado especifico do modelo de dados e alimenta a variavel data para o registro poder entao ser manipulado
	// recebe:
	// retorna:
	public function read($value, $recursion=0)
	{
	
		$result = $this->find($value, NULL, $recursion);
	
		if( ! empty($result) )
		{

			$this->data = $result[0][ $this->name ][0];

			return TRUE;

		}
	
		return FALSE;
	
	}

	// remover as funcoes e usar create table de DatabaseCommon
	public function createStage()
	{
	
		if ( $this->Db->stageExists($this->stagename) )
		{
			return FALSE; // verifica se a tabela existe. se existir, retorna falso
		}
	
		return $this->Db->query($this->Db->table($this->stagename, $this->schema)->sql('create'));
	
	}

	public function loadFromStage()
	{

		$sql = "SELECT
c.column_name AS 'name',
c.data_type AS 'type',
c.column_type AS 'size',
(SELECT st.constraint_type FROM information_schema.table_constraints st INNER JOIN information_schema.key_column_usage sk ON (st.constraint_name=sk.constraint_name AND st.table_name=sk.table_name AND st.table_schema=sk.table_schema) WHERE st.constraint_type='PRIMARY KEY' AND sk.column_name=c.column_name AND st.table_name=c.table_name AND st.table_schema=c.table_schema) AS 'primary',
(SELECT st.constraint_type FROM information_schema.table_constraints st INNER JOIN information_schema.key_column_usage sk ON (st.constraint_name=sk.constraint_name AND st.table_name=sk.table_name AND st.table_schema=sk.table_schema) WHERE st.constraint_type='UNIQUE' AND sk.column_name=c.column_name AND st.table_name=c.table_name AND st.table_schema=c.table_schema) AS 'unique',
k.referenced_table_name AS 'foreign',
c.is_NULLable AS 'notnull',
sc.extra AS 'sequence',
c.column_default AS 'default'
FROM information_schema.columns c
LEFT JOIN information_schema.columns sc ON (sc.extra='auto_increment' AND sc.column_name=c.column_name AND sc.table_name=c.table_name AND sc.table_schema=c.table_schema)
LEFT JOIN information_schema.key_column_usage k ON (k.table_schema=c.table_schema AND k.table_name=c.table_name AND k.column_name=c.column_name AND k.referenced_table_name IS NOT NULL)
WHERE c.table_schema='" . $this->_dbsettings['name'] . "' AND c.table_name='" . $this->stagename . "'
ORDER BY c.ordinal_position;";

		$result = $this->_fetch($sql);
	
		if ( ! empty($result) )
		{

			$schema = array();

			foreach ($result as $v )
			{

				if( $v['primary'] ) // se se eh chave primaria, tenta encontrar os lugares para onde ela propaga
				{

					$sql = "SELECT DISTINCT table_name AS 'tablename' FROM information_schema.key_column_usage WHERE referenced_table_name='" . $this->getName() . "' AND referenced_column_name='{$v['name']}'";

					$primary_refs = $this->_fetch($sql);

					if( ! empty($primary_refs) )
					{

						$temparray = array();

						foreach($primary_refs as $k => $ref)
						{
							$temparray[] = $ref['tablename'];
						}

						$v['primary'] = $temparray;

					}
					else
					{
						$v['primary'] = TRUE;
					}

				}
				else
				{
					unset($v['primary']);
				}

				if( $v['type'] )
				{
					if( $v['type']=='tinyint' )
					{
						$v['type'] = 'bool';
					}
					elseif( $v['type']=='int' )
					{
						$v['type'] = 'interger';
					}
				}

				if( $v['notnull']=='NO' )
				{
					$v['notnull'] = TRUE;
				}
				else
				{
					unset($v['notnull']);
				}

				if( $v['unique']=='UNIQUE' )
				{
					$v['unique'] = TRUE;
				}
				else
				{
					unset($v['unique']);
				}

				if( $v['sequence']=='auto_increment' )
				{
					$v['sequence'] = 1;
				}
				else
				{
					unset($v['sequence']);
				}

				if( ! $v['foreign'] )
				{
					unset($v['foreign']);
				}

				if( ! $v['default'] )
				{
					unset($v['default']);
				}

				if ( preg_match("~\(([0-9]+)\)~", $v['size'], $match) )
				{
					$v['size'] = $match[1];
				}

				$name = $v['name'];

				unset($v['name']);

				$schema[ $name ] = $v;

			}

			return $schema;

		}
	
	}

	/**
	* Executa uma seleção no banco de dados
	* de acordo com os parâmetros passados.
	*/
	protected function _get(Array $filter=NULL, Array $order=NULL, Array $fields=NULL, $pagesize=NULL)
	{
	
		$sql = 'SELECT ';
	
		$sql .= ( $fields ) ? implode(', ', $fields) : '*';
	
		$sql .= ' FROM ' . $this->stagename;
	
		if( $filter )
		{
			$sql .= ' WHERE ' . $this->__where($filter);	
		}
	
		if( $order )
		{
			$sql .= ' ORDER BY ' . $this->__order($order);	
		}
	
		if( is_numeric($pagesize) )
		{
			$sql .= ' LIMIT ' . $pagesize;
		}
	
		$sql .= ';';

		return $this->_fetch($sql);
	
	}

	/**
	* Executa uma query e retona o array de resultados.
	*/
	protected function _query($query)
	{	

		return mysql_query($query, $this->_conn);
	
	}

	/**
	* Executa uma query e retorna em array.
	*/
	protected function _fetch($query)
	{
	
		$result = $this->_query($query);

		if( $result )
		{
		
			$table = array();
		
			while( $row = mysql_fetch_assoc($result) )
			{
				$table[] = $row;
			}
		
			return $table;
		
		}
	
		return;
	}

	/**
	* Transforma um array em cláusula Where do SQL.
	*/
	private function __where(Array $filter)
	{
	
		$sql = '';
		
		$size = count($filter) - 1;

		$i = 0;
	
		foreach($filter as $k => $v)
		{

			if( is_array($v) && is_numeric($k) )
			{
				
				$logical = ' OR ';
				
				$sql .= '(' . $this->__where($v) . ')';
				
			}
			else
			{
				
				if( preg_match('/(.*)\ ([^\ .]*)$/', $k, $match) )
				{
					
					$k = $match[1];
					
					$op = $match[2];
					
				}
				else
				{
					$op = '=';
				}

				$logical = ' AND ';

				if( is_array($v) )
				{
					
					$wheres_temp = array();
					
					foreach($v as $each_value)
					{
						$wheres_temp[] = $this->__where(array($k=>$each_value));
					}
					
					$sql .= implode($logical, $wheres_temp);
					
				}
				else
				{
					$sql .= "`{$k}`{$op}" . $this->__parseValue($v);
				}

			}
			
			if( $i < $size )
			{
				$sql .= $logical;
			}
			
			$i++;
			
		}

		return $sql;
	
	}

	/**
	* Transforma um array em cláusula Order do SQL.
	*/
	private function __order(Array $order)
	{
	
		$sql = '';
	
		foreach($order as $k => $v)
		{
		
			if( is_numeric($k) )
			{
			
				$k = $v;
			
				$v = 'ASC';
			
			}
		
			$v = strtoupper($v);
		
			if( in_array($v, array('ASC', 'DESC')) )
			{
				$sql .= '`' . $k . '` ' . $v;	
			}
		}
	
		return $sql;
	
	}

	/**
	* Esse método basicamente recebe uma variável
	* e transforma no tipo ideal para ser usado
	* no SQL baseando-se no tipo da variável.
	*/
	private function __parseValue($v)
	{
	
		if( is_string($v) )
		{
			$v = "'{$v}'";
		}
		elseif( is_array($v) )
		{
			$v = '(' . implode(', ', $v) . ')';
		}
	
		return (string)$v;
	
	}

}