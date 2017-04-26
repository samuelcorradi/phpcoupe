<?php

namespace DataScribe;

class ActiveRecord
{

	public $data;

	/**
	*
	*/
	protected $_table;

	/**
	*
	*/
	public function __construct(\DataScribe\Table & $table)
	{

		$this->_table = $table;

	}

	public function save()
	{

	}

}