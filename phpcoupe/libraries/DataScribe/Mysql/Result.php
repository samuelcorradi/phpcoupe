<?php

namespace DataScribe\Mysql;

final class Result extends \DataScribe\Result
{

	public function begin()
	{

		return $this->_db->query('START TRANSATION;');

	}

	public function commit()
	{

		return $this->_db->query('COMMIT;');

	}

	public function rollback()
	{

		return $this->_db->query('ROLLBACK;');

	}
	
	/**
	* Executa uma query em banco de retorna o ponteiro.
	*/
	protected function _query()
	{

		return mysql_query($this->_query, $this->_db->connect()->getHandler());
		
	}

	/**
	* Retorna a quantidade de resultados encontrados na ultima consulta.
	*/
	protected function _count()
	{

		return mysql_num_rows($this->_handler);

	}

	protected function _fetch()
	{

		$result = array();

		while ( $row = mysql_fetch_assoc($this->_handler) )
		{
			$result[] = $row;
		}

		return $result;
		
	}
	
	/**
	* Reinicia o ponteiro para resultados no banco.
	*/
	protected function _dataSeek($pos)
	{

		return @ mysql_data_seek($this->_handler, $pos);

	}
	
	protected function _affectedRows()
	{
		
		return mysql_affected_rows($this->_db->connect()->getHandler());

	}
	
	protected function _lastId()
	{

		return mysql_insert_id();

	}
	
}

?>
