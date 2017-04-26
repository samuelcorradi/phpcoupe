<?php

namespace DataScribe\Mysql;

final class Adapter extends \DataScribe\Adapter
{

	/**
	* Faz uma chamada ao banco.
	*/
	public function query($sql)
	{
		
		if( $this->isConnected() )
		{

			$this->_result = mysql_query($sql, $this->_connection);

			if( is_resource($this->_result) )
			{
				return $this;
			}

		}

	}
	
	public function createTableAs($tablename, \DataScribe\Select & $select)
	{

		return $this->query("CREATE TABLE {$tablename} AS " . $select->sql());

	}
	
	public function begin()
	{

		return $this->transition_started = $this->query('START TRANSATION;');

	}

	public function commit()
	{

		$this->transition_started = ! $this->query('COMMIT;');

		return ! $this->transition_started;

	}

	public function rollBack()
	{

		$this->transition_started = ! $this->query('ROLLBACK;');

		return ! $this->transition_started;

	}
	
	/**
	* Retorna uma linha do resultado.
	*/
	public function fetchRow()
	{
		if( $this->hasResult() )
		{
			return mysql_fetch_assoc($this->_result);
		}
	}

	public function connect()
	{
		
		/*
		* por padrão mysql_connect() retorna a
		* conexão já aberta se os dados de conexão
		* forem os mesmos. porem, servidores diferentes
		* podem ter os mesmo dados requeridos por essa
		* funcao (endereco fisico server, nome, senha).
		* Essa variavel armazena um hash que inclui tambem
		* o nome do bando de dados mysql para fazer
		* distincao dessas conexões.
		*/
		static $pool;
		
		/* 
		* Cria um MD5 único com a função de armazenar os links.
		* Isso por que, se o servidor for diferente, mas o usuário
		* for igual, a função mysql_connect() retorna uma cópia do links
		* já existente. Ignornando caso o banco seja outro.
		*/
		$poolid = md5($this->_config['host'] . $this->_config['user'] . $this->_config['schema']);

		if ( ! isset($pool[ $poolid ]) )
		{		
			$pool[ $poolid ] = mysql_connect($this->_config['host'], $this->_config['user'], $this->_config['pass'], TRUE) or trigger_error(mysql_error(), E_USER_ERROR);
		}

		if ( mysql_select_db($this->_config['schema'], $pool[ $poolid ]) )
		{
			return $this->_connection = $pool[ $poolid ];
		}

	}

	public function disconnect()
	{

		return mysql_close($this->_connection);

	}

	public function version()
	{

		$this->query('SELECT version() AS version;');
		
		if ( $this->hasResult() )
		{
			
			$row = $this->fetchRow();
		
			return $row['version'];
		
		}
		
		return FALSE;

	}

	/**
	* @return array Lista de tabelas da conexão.
	*/
	public function listTables()
	{
	
		$this->query("SELECT st.table_name FROM information_schema.tables st WHERE st.table_schema='" . $this->schema . "';");

		if ( $this->hasResult() )
		{
			
			$tables = array();

			while ( $row = $this->fetchRow() )
			{
				$tables[] = $row['table_name'];
			}

			return $tables;
	
		}
	
		return FALSE;
	
	}

}