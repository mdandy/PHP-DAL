<?php

/**
 * This class represents a table schema.
 * @author Michael Dandy
 */
class TableSchema
{
	public $table_name = "";
	public $version = 0;
	public $column_def = array();
	public $primary_def = array();
	
	/**
	 * Constructor
	 * @param string $table_name The name of the table
	 * @param int $version [Optional] The version of the table. It is defaulted to 0.
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
	
	/**
	 * Get the column names of this table.
	 * @return Array of column names of this table
	 */
	public function getColumnNames()
	{
		$columns = array();
		for($i = 0; $i < count($this->column_def); $i++)
		{
			$def = explode(" ", $this->column_def[$i]);
			array_push($columns, $def[0]);
		}
		return $columns;
	}
	
	/**
	 * Get the primary keys of this table.
	 * @return The primary keys of this table
	 */
	public function getPrimaryKeys()
	{
		return $this->primary_def;
	}
}

?>