<?php

/**
* Formulário encarado como uma fonte de dados estende \Habilis\Datasource.
*/

namespace Habilis\Datasource;

class Form extends \Habilis\Datasource
{
	
	protected $_fields;
	
	/**
	* Adiciona um campo ao formulário.
	*/
	public function setField($name, Array $attr)
	{
		
		$this->_fields[ $name ] = $attr;
		
	}
	
}