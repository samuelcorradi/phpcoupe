<?php

namespace Habilis\Datasource;

class Db extends \Habilis\Datasource
{
	
	/**
	* Determina se as operações são transacionais.
	*/
	public $transactional = FALSE;
	
	/**
	* Armazena objeto com o ponteiro de conexão.
	*/
	protected $_adapter;

	/**
	* Armazena o objeto que contém o resultado da última consulta ao banco de dados.
	*/
	protected $_last_result;

	/**
	* Armazena os resultados de consultas já executadas.
	*/
	protected $_cache;

	/**
	* Conecta instanciando o objeto de conexão.
	*/
	public function connect()
	{

		if( ! $this->_adapter )
		{

			$class = "DataScribe\\" . ucfirst(strtolower($this->type)) . '\Adapter';

			$this->_adapter = new $class($this->connection);

			$this->_adapter->connect();

		}

		return $this->_adapter;
		
	}
	
	/**
	* Desconecta destruindo o objeto de conexão.
	*/
	public function disconnect()
	{
		
		$this->_adapter != $this->_adapter->disconnect();
	
	}

	/**
	* Executa uma chamada ao banco de retorna o objeto de resultado
	*/
	public function query($query, $cache=TRUE)
	{
		
		return $this->_adapter->query($query)->fetchAll();

		$query_id = md5($query);
	
		if ( $cache && isset($this->_cache[ $query_id ]) )
		{
			$this->_last_result = $this->_cache[ $query_id ];
		}
		else
		{

			// $class = '\Habilis\Db\\' . ucfirst(strtolower($this->type)) . '\Result';

			// $this->_last_result = new $class($query, $this);
			
			$this->_last_result = $this->_adapter->query($query);
			
			if ( $cache )
			{
				$this->_cache[ $query_id ] = $this->_last_result;
			}

		}

		return $this->_last_result;
	
	}

	/**
	* Retorna o resultado da ultima consulta.
	*/
	public function & lastResult()
	{

		return $this->_last_result;

	}	
	
	/**
	* Limpa a coleção de resultados do cache.
	*
	* @return true
	*/
	public function clearCache($query=NULL)
	{
	
		if ( ! $query )
		{
			$this->_cache = array();
		}
		else
		{
			unset($this->_cache[ md5($query) ]);
		}
		
		return TRUE;
	
	}
	
	/**
	* Factory para objetos do tipo select.
	*/
	public function select()
	{

		$class = '\DataScribe\\' . ucfirst(strtolower($this->type)) . '\Select';
	
		return new $class($this->_adapter);
	
	}
	
	/**
	* Cria um filtro.
	*/
	public function filter(Array $filter=NULL)
	{
		
		$filter_class = '\DataScribe\\' . ucfirst(strtolower($this->type)) . '\Filter';
		
		$expression = new $filter_class();

		foreach ((array)$filter as $a => $b)
		{

			if( is_array($b) && is_numeric($a) )
			{

				$new = $this->filter($b);

				$expression->or($new);

			}
			else
			{

				if( is_string($a) && preg_match('/(.*)\ ([a-z]*)$/', $a, $matches) )
				{

					$a = $matches[1];

					$op = strtoupper($matches[2]);

				}
				else
				{

					$op = 'EQUAL';

				}

				if ( is_null($b) ) /* Se o valor for do tipo NULL coloca o operador IS. mas se o operador for diff, coloca IS NOT. */
				{

					$op = ( $op=='DIFF' ) ? 'ISNOT' : 'IS';

					$expression->and($a)->setOperator($op)->setValue($b);

				}
				elseif ( is_array($b) ) /* Se o valor for do tipo ARRAY coloca o operador INSIDE (gerando IN). mas se o operador for diff, coloca OUTSIDE (gerando NOT IN) */
				{

					if ( $op=='EQUAL' )
					{
						$expression->setValue($a)->inside($b);
					}
					elseif ( $op=='DIFF' )
					{
						$expression->setValue($a)->outside($b);
					}
					else
					{

						$new_filter =  $this->filter();

						foreach($b as $new_b)
						{

							$new_a = $a . ' ' . $op;

							$new_criteria = $this->filter(array($new_a=>$new_b));

							$new_filter->or($new_criteria);

						}

						$expression->and($new_filter);

					}

				}
				elseif( is_string($b) && preg_match('/\%.*\%/', $b) )
				{

					$expression->and($a)->similar($b); /* Se valor tive %% nas extremidades, interpreta como um LIKE. */

				}
				else
				{

					$expression->and($a)->setOperator($op)->setValue($b);

				}

			}

		}
		
		return $expression;

	}
	
	/**
	* Verifica se determinada tabela existe ou não.
	*
	* @param string Nome da tabela.
	* @return bool Verdadeiro se existe ou não.
	*/
	public function tableExists($tablename)
	{
	
		$list = $this->_adapter->listTables();
	
		return in_array($tablename, $list);
	
	}
	
	/**
	* Faz o papel de Factory para as tabelas.
	*/
	public function table($tablename)
	{

		$tableclass = '\DataScribe\\' . ucfirst(strtolower($this->type)) . '\Table';

		return $tableclass::load($this, $tablename);

	}
	
}