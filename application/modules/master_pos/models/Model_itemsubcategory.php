<?php
class Model_ItemSubCategory extends DB_Model {
	
	public $table;
	
	function __construct()
	{
		parent::__construct();	
		$this->table = $this->prefix.'item_subcategory';
	}
	
	

} 