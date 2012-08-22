<?php

class TableSchema
{
	public $table_name = "";
	public $version = 0;
	public $column_def = array();
	public $primary_def = array();
	
	/**
	 * Constructor
	 * @param string $table_name The name of the table
	 */
	public function __construct($table_name, $version = 0) 
	{
		$this->table_name = $table_name;
		$this->version = $version;
	}
	
	/**
	 * Add column definition to this table.
	 * @param string $column_name The name of the column
	 * @param string $datatype The datatype of the column
	 * @param int $size [Optional] The data size of the column only for supported datatype
	 */
	public function addColumnDefinition($column_name, $datatype, $size = NULL)
	{
		$column = "";
		if ($size != NULL)
			$column = "$column_name $datatype($size)";
		else
			$column = "$column_name $datatype";
		array_push($this->column_def, $column);
	}
	
	/**
	 * Add primary key definition to this table.
	 * @param string $column_name The name of the column to be used as primary key
	 */
	public function addPrimaryKeyDefinition($column_name)
	{
		array_push($this->primary_def, $column_name);
	}
}

?>