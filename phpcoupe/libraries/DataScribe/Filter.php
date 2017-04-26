<?php

namespace DataScribe;

class Filter
{

	/**
	* Tradução para operadores.
	*/
	protected $_operators = array();

	/**
	* Tradução para operadores lógicos.
	*/
	protected $_logical_operators = array();

	/**
	* Armazena os critérios.
	* @access protected
	*/
	protected $_expression = array();
	
	/**
	* Imprimir para string.
	*/
	public function __toString()
	{

		return $this->sql();

	}
	
	/**
	* Qualquer método chamada que não existir
	* será encarado como um operador que
	* tem o nome igual ao nome do método chamado.
	* Se um parâmetro foi passado ao método,
	* o valor do parâmetro é encarado como um valor.
	* Dessa for é possível adicionar operadores e
	* valores em cadeia. Exemplo:
	* $filter->setValue(5)->plus(6)->equal(11);
	* Isso é o mesmo que 5 + 6 = 11.
	*/
	public function __call($name, $args)
	{

		$this->setOperator($name);

		if( isset($args[0]) )
		{
			$this->setValue($args[0]);
		}
		
		return $this;

	}

	/**
	* Adiciona um valor qualquer a expressão.
	*/
	public function setValue($value)
	{
		
		$this->_expression[] = $value;
		
		return $this;
		
	}

	/**
	* Adicionar um operador. Antes, verificar se o operador
	* passado existe na tabela de operadores da classe
	* e, caso exista, substitui o valor passar pelo valor
	* correspondente de acordo com a tabela.
	*/
	public function setOperator($op)
	{

		$op = strtoupper($op);

		if( isset($this->_logical_operators[ $op ]) )
		{

			if( empty($this->_expression) )
			{
				return $this;
			}

			$op = $this->_logical_operators[ $op ];

		}
		elseif( isset($this->_operators[ $op ]) )
		{

			$op = $this->_operators[ $op ];

		}

		$this->setValue($op);

		return $this;

	}
	
	public function sql()
	{
		
		if( empty($this->_expression) )
		{
			return '';
		}
		
		return "(" . implode(' ', $this->_expression) . ")";
		
	}

}