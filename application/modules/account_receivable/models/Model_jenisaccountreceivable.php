<?php
class Model_jenisaccountreceivable extends DB_Model {
	
	public $table;
	
	function __construct()
	{
		parent::__construct();	
		$this->table = $this->prefix.'autoposting';
	}
	
	

} 