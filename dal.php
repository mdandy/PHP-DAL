<?php

require_once("dbconfig.php");

/**
 * This class provides a Data Access Layer services to the Database using PDO
 * library.
 * @see http://php.net/manual/en/book.pdo.php
 */
class DAL
{
	private static $schema_table = "sys_schema";
	private static $dbh;	// database handler
	
	/**
	 * Connect to MySQL database
	 * @return true on success or false otherwise
	 */
	public static function connect()
	{
		if(!self::$dbh) 
		{
			try 
			{
				$host = HOST;
				$db_name = DB_NAME;
				$username = USERNAME;
				$password = PASSWORD;
				
				// Establish connection
				self::$dbh = new PDO("mysql:host=".$host.";dbname=".$db_name, $username, $password);
				self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				if (self::$dbh != NULL)
					self::init();
			}
			catch(PDOException $e) 
			{
				//echo ("Error: " . $e->getMessage());
				return false;
			}
		}
		return (self::$dbh != NULL);
	}
	
	/**
	 * Check if there is an open connection to the database.
	 * @return true if there is an open connection to the database or false otherwise
	 */
	public static function isConnected()
	{
		if(self::$dbh == NULL)
			return false;
		return true;
	}
	
	/**
	 * Disconnect from the database
	 */
	public static function disconnect()
	{
		self::$dbh = NULL;
	}
	
	/**
	 * Initialize database schema.
	 */
	private static function init()
	{
		$schema_table_name = self::$schema_table;
		$sql = "CREATE TABLE IF NOT EXISTS $schema_table_name (";
		$sql .= "table_name varchar(255), ";
		$sql .= "column_name varchar(255), ";
		$sql .= "datatype varchar(255), ";
		$sql .= "iskey smallint, ";
		$sql .= "version int, ";
		$sql .= "PRIMARY KEY (table_name, column_name)";
		$sql .= ")";
		self::$dbh->exec($sql);
	}
	
	/**
	 * Add table definition to schema table.
	 * @return true on success or false otherwise
	 */
	private static function addToSchema($table)
	{
		try
		{
			$isSuccessful = true;

			$schema_table_name = self::$schema_table;
			$sql = "INSERT INTO $schema_table_name (table_name, column_name, datatype, iskey, version)"; 
			$sql .= " VALUES (:table_name, :column_name, :datatype, :iskey, :version)";
			$sql .= " ON DUPLICATE KEY UPDATE";
			$sql .= " table_name=:table_name, column_name=:column_name, datatype=:datatype, iskey=:iskey, version=:version";
			
			$query = self::$dbh->prepare($sql);
			$query->bindParam(":table_name", $mTableName, PDO::PARAM_STR, 255);
			$query->bindParam(":column_name", $mColumnName, PDO::PARAM_STR, 255);
			$query->bindParam(":datatype", $mDatatype, PDO::PARAM_STR, 255);
			$query->bindParam(":iskey", $mIsKey, PDO::PARAM_INT);
			$query->bindParam(":version", $mVersion, PDO::PARAM_INT);
			
			for($i = 0; $i < count($table->column_def); $i++)
			{
				$mTableName = $table->table_name;
				$mColumnName = self::parseColumnName($table->column_def[$i]);
				$mDatatype = self::parseDatatype($table->column_def[$i]);
				$mIsKey = 0;
				$mVersion = $table->version;
				
				if (in_array($mColumnName, $table->primary_def))
				    $mIsKey = 1;
				
				$ret = $query->execute();
				$isSuccessful = $isSuccessful || $ret;
			}
			return $isSuccessful;
		}
		catch(PDOException $e) 
		{
			//echo ("Error: " . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * Parse column name from the column defition.
	 * @return The column name
	 */
	private static function parseColumnName($column_def)
	{
		$def = explode(" ", $column_def);
		return $def[0];
	}
	
	/**
	 * Parse column datatype with the size from the column defition.
	 * @return The column datatype
	 */
	private static function parseDatatype($column_def)
	{
		$def = explode(" ", $column_def);
		return $def[1];
	}
	
	/**
	 * Parse column datatype without the size from the datatype defition.
	 * @return The column datatype
	 */
	private static function parseDatatype2($datatype_def)
	{
		$def = explode("(", $datatype_def);
		return $def[0];
	}
	
	/**
	 * Parse column size from the datatype defition.
	 * @return The column size
	 */
	private static function parseSize($datatype_def)
	{
		$def = explode("(", $datatype_def);
		$size = substr($def[1], 0, -1);
		return intval($size);
	}
	
	/**
	 * Create a table and add it to the schema table
	 * @param TableSchema $table The table schema to be created
	 * @return true on sucess or false otherwise
	 */
	public static function createTable($table)
	{
		
		try
		{
			$table_name = $table->table_name;
			$sql = "CREATE TABLE IF NOT EXISTS $table_name (";
		
			for($i = 0; $i < count($table->column_def); $i++)
			{
				$sql .= $table->column_def[$i];
				if ($i != count($table->column_def) - 1)
					$sql .= ", ";
			}
		
			if (count($table->primary_def) > 0)
			{
				$sql .= ", PRIMARY KEY (";
				for($i = 0; $i < count($table->primary_def); $i++)
				{
					$sql .= $table->primary_def[$i];
					if ($i != count($table->primary_def) - 1)
						$sql .= ", ";
				}
				$sql .= ")";
			}
			$sql .= ")";
			self::$dbh->exec($sql);
			
			// Add the table to schema
			$isSuccessful = self::addToSchema($table);
			return $isSuccessful;
		}
		catch(PDOException $e) 
		{
			//echo ("Error: " . $e->getMessage());
		}
		return false;
	}
	
	/**
	 * Get the table schema.
	 * @return The table schema object
	 */
	public function getTableSchema($tableName)
	{
		try
		{
			$schema_table_name = self::$schema_table;
			$sql = "SELECT * FROM $schema_table_name WHERE table_name=:table_name";
			$query = self::$dbh->prepare($sql);
			$query->bindParam(":table_name", $tableName, PDO::PARAM_STR, 255);
			$query->execute();

			if ($query->rowCount() > 0)
			{
				$table = new TableSchema($tableName);
				while ($result = $query->fetch())
				{
					$column_name = $result["column_name"];
					$datatype_def = $result["datatype"];
					$datatype = self::parseDatatype2($datatype_def);
					$size = self::parseSize($datatype_def);
					$iskey = $result["iskey"];
					$version = $result["version"];
					
					$table->addColumnDefinition($column_name, $datatype, $size);
					if ($iskey == 1)
						$table->addPrimaryKeyDefinition($column_name);
					$table->version = $version;
				}
				return $table;
			}
		}
		catch(PDOException $e) 
		{
			//echo ("Error: " . $e->getMessage());
		}
		return NULL;
	}
	
	/**
	 * Get the table version.
	 * @return The table version or -1 if the table does not exist
	 */
	public function getTableVersion($tableName)
	{
		try
		{
			$schema_table_name = self::$schema_table;
			$sql = "SELECT version FROM $schema_table_name WHERE table_name=:table_name LIMIT 1";
			$query = self::$dbh->prepare($sql);
			$query->bindParam(":table_name", $tableName, PDO::PARAM_STR, 255);
			$query->execute();
			
			if ($query->rowCount() > 0)
			{
				$version = $query->fetch();
				return $version["version"];
			}
			return -1;
		}
		catch(PDOException $e) 
		{
			//echo ("Error: " . $e->getMessage());
		}
		return -1;
	}
	
	/**
	 * Check if a table exist in the database.
	 * @return true if the table exists or false othewise
	 */
	public function isTableExist($tableName)
	{
		$version = self::getTableVersion($tableName);
		if ($version == -1)
			return false;
		return true;
	}
	
	public function insert()
	{
		
	}
	
	public function update()
	{
		
	}
	
	public function delete()
	{
		
	}
}

?>