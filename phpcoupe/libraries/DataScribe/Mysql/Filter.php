<?php

namespace DataScribe\Mysql;

class Filter extends \DataScribe\Filter
{

	/**
	* Tradução para operadores.
	*/
	protected $_operators = array(
		'PLUS'=>'+',
		'LESS'=>'-',
		'MULTI'=>'*',
		'DIV'=>'/',
		'EQUAL'=>'=',
		'MAJEQL'=>'>=',
		'MINEQL'=>'<=',
		'DIFF'=>'<>',
		'MAJOR'=>'>',
		'MINOR'=>'<',
		'LIKE'=>'LIKE',
		'IN'=>'IN',
		'NOTIN'=>'NOT IN',
		'IS'=>'IS',
		'ISNOT'=>'IS NOT',
		'BETWEEN'=>'BETWEEN'
		);

	/**
	* Tradução para operadores lógicos.
	*/
	protected $_logical_operators = array(
		'OR'=>'OR',
		'AND'=>'AND',
		'XOR'=>'XOR',
		);

}