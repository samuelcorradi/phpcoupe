<?php

namespace DataScribe\Mysql;

final class Table extends \DataScribe\Table
{
	
	public static function create(\DataScribe\Table & $table)
	{

	}

	/**
	* Engine usado na criação das tabelas.
	*/
	public $engine;
	
	public function addField($name, Array $attr=NULL)
	{
		if( ! $this->fieldExists($name) )
		{
			
			if( $attr )
			{
				$param = ' ' . $this->_fieldParam($attr);
			}
			
			return $this->_adapter->query('ALTER TABLE ' . $this->_name . ' ADD ' . $name . $param . ';');
		
		}
	}
	
	public function changeField($name, Array $attr=NULL)
	{
		if( ! $this->fieldExists($name) )
		{
			
			if( $attr )
			{
				$param = ' ' . $this->_fieldParam($attr);
			}
			
			return $this->_adapter->query('ALTER TABLE ' . $this->_name . ' ADD ' . $name . $param . ';');
		
		}
	}
	
	public function dropField($name, Array $attr=NULL)
	{
		if( $this->fieldExists($name) )
		{
			return $this->_adapter->query('ALTER TABLE ' . $this->_name . ' DROP ' . $name . ';');
		}
	}
	
	protected function _fieldParam(Array $attr)
	{
		
		if( isset($attr['type']) )
		{
			
			$sql = $attr['type'];

			if( isset($attr['size']) )
			{
				$sql .= '(' . $attr['size'] . ')';
			}

			$sql .= ( isset($attr['notnull']) && $attr['notnull']==TRUE ) ? ' NOT NULL' : '';

			if( isset($attr['default']) )
			{
				$sql .= ' DEFAULT ' . $attr['default'];
			}
		
			if ( isset($attr['sequence']) && $attr['sequence']==TRUE )
			{
				$sql .= ' AUTO_INCREMENT';
			}
			
			return $sql;
		
		}
		
	}
	
	
	/**
	* Cria um índice.
	* Recebe como parâmetro um array com a chave columns que
	* possui um array associativo com campos e direção de
	* ordenação do índice. Exemplo de como deve ser o parâmetro:
	* array('columns'=>array('columnX'=>'asc', 'columnY'=>'desc'));
	*/
	public function addIndex($name, Array $attr=NULL)
	{
		
		if( isset($attr['columns']) )
		{
			foreach((array)$attr['columns'] as $k => $v)
			{
				
				$temp = array();
				
				if( is_numeric($k) )
				{
					$k = $v;
					$v = 'desc';
				}
				
				$temp[] = "`{$k}` ". $v;
			
			}
		}
		
		return 'CREATE INDEX ' . $name . ' ON ' . $this->_name . '( ' . implode(', ', $temp) . ');';

	}
	
	/**
	* Remove um índice.
	*/
	public function dropIndex($name)
	{

		return 'DROP INDEX ' . $name . ' ON ' . $this->_name . ';';

	}
	
	/**
	* Descreve a estrutura da tabela.
	*/
	public function info()
	{

		$query = "SELECT
c.column_name AS 'name',
c.data_type AS 'type',
c.column_type AS 'size',
(SELECT st.constraint_type FROM information_schema.table_constraints st INNER JOIN information_schema.key_column_usage sk ON (st.constraint_name=sk.constraint_name AND st.table_name=sk.table_name AND st.table_schema=sk.table_schema) WHERE st.constraint_type='PRIMARY KEY' AND sk.column_name=c.column_name AND st.table_name=c.table_name AND st.table_schema=c.table_schema) AS 'primary',
(SELECT st.constraint_type FROM information_schema.table_constraints st INNER JOIN information_schema.key_column_usage sk ON (st.constraint_name=sk.constraint_name AND st.table_name=sk.table_name AND st.table_schema=sk.table_schema) WHERE st.constraint_type='UNIQUE' AND sk.column_name=c.column_name AND st.table_name=c.table_name AND st.table_schema=c.table_schema) AS 'unique',
k.referenced_table_name AS 'foreign',
c.is_nullable AS 'notnull',
sc.extra AS 'sequence',
c.column_default AS 'default'
FROM information_schema.columns c
LEFT JOIN information_schema.columns sc ON (sc.extra='auto_increment' AND sc.column_name=c.column_name AND sc.table_name=c.table_name AND sc.table_schema=c.table_schema)
LEFT JOIN information_schema.key_column_usage k ON (k.table_schema=c.table_schema AND k.table_name=c.table_name AND k.column_name=c.column_name AND k.referenced_table_name IS NOT NULL)
WHERE c.table_schema='" . $this->_adapter->getConfig('schema') . "' AND c.table_name='" . $this->_name . "'
ORDER BY c.ordinal_position;";

		$result = $this->_adapter->fetch($query);

		if ( ! empty($result) )
		{
			
			$schema = array();
			
			foreach ($result as $v )
			{
				
				if( ! empty($v['primary']) ) // se se eh chave primaria, tenta encontrar os lugares para onde ela propaga
				{
					
					$query = "SELECT DISTINCT table_name AS 'tablename' FROM information_schema.key_column_usage WHERE referenced_table_name='" . $this->getName() . "' AND referenced_column_name='{$v['name']}'";
					
					$primary_refs = $this->_adapter->fetch($query);
					
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
				
				if( $v['foreign']=='' )
				{
					unset($v['foreign']);
				}
				
				if( $v['default']=='' )
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
	
}

?>
