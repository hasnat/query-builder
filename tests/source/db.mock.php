<?php
require_once dirname(__FILE__) . '/../../source/db.interface.php';

class mockQueryBuilderDb implements queryBuilderDbInterface{

	/** {@inheritDoc} */
	public function escape($value){
		return $value;
	}

	/** {@inheritDoc} */
	public function quote()
	{
		return "'";
	}

	/** {@inheritDoc} */
	public function field_quote()
	{
		return "`";
	}
}
