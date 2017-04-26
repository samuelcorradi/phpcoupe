<?php

namespace DataScribe\Mysql;

final class Select extends \DataScribe\Select
{
	
	public function sql()
	{
		
		$sql = 'SELECT ';
		
		if( $this->distinct )
		{

			$sql .= 'DISTINCT ';

		}

		$join = '';

		$filter = '';
		
		$col_temp = array();
		
		$table_temp = array();
		
		/* Columns. */
		
		foreach($this->_column as $col)
		{

			if( isset($col[0]) )
			{

				if( is_array($col[0]) )
				{
					
					$tablename = $col[0][ key($col[0]) ];
					
					$table_temp[] = $col[0][ key($col[0]) ] . ' ' . key($col[0]);
				
				}
				else
				{
					
					$tablename = $col[0];
					
					$table_temp[] = $col[0];
				
				}
				
				if( isset($col[1]) )
				{
					
					foreach($col[1] as $key => $columns)
					{
						
						if( is_string($key) )
						{
							$col_temp[] = $tablename . '.' . $key . ' AS ' . $columns;
						}
						else
						{
							$col_temp[] = $tablename . '.' . $columns;
						}
					
					}
					
				}
				else
				{
					$col_temp[] = $tablename . '.*';
				}
				
			}
			
		}
		
		$sql .= implode(', ', $col_temp) . ' FROM ' . implode(', ', $table_temp);
		
		/* Join. */

		if( ! empty($this->_join) )
		{

			$joins=array();

			foreach ( $this->_join as $join )
			{

				$tablename = ( is_array($join[0]) ) ? $join[0] . ' ' . key($join[0]) : $join[0];

				$filter = $join[1]->sql();

				$type = ( $join[3] ) ? strtoupper($join[3]) : 'INNER';

				$joins[] = $type . " JOIN " . $tablename . ' ON ' . $filter;

			}

			$sql .= ' ' . implode(' ', $joins);

		}

		/* Where. */

		if ( $this->_filter )
		{

			$sql .= ' WHERE ' . $this->_filter->sql();

		}
		
		/* Order. */
		
		if ( $this->_order )
		{
			
			$order_temp = array();
			
			foreach($this->_order as $field => $direction)
			{
				$order_temp[] = $field . ' ' . strtoupper($direction);
			}
			
			$sql .= ' ORDER BY ' . implode(', ', $order_temp);
			
		}
		
		/* Group. */
		
		if ( $this->_group )
		{
			
			$sql .= ' GROUP BY ' . implode(', ', $this->_group);
			
		}
		
		/* Pagesize. */

		if ( $this->pagesize )
		{

			$sql .= ' LIMIT ' . $Select->pagesize;

		}
		
		/* Limit. */

		if ( $this->pagenumber )
		{

			$sql .= ' OFFSET ' . $Select->pagenumber;

		}

		return $sql . ';';
		
	}
	
}

?>
