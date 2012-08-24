<?php

require_once("dal.php");
require_once("table_dal.php");

// Disallow any other request method other than POST
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (isset($_POST["module"]))
	{
		$module = $_POST["module"];
		$output = "";
		DAL::connect();
		
		// Connection test
		$output .= printTestSuite("Connection");
		$output .= beginTestCase();
		$output .= printTestCase("isConnected", niceBoolean(DAL::isConnected()));
		$output .= endTestCase();	
		
		if (strcmp($module, "all") == 0 || strcmp($module, "create_table") == 0)
		{
			$output .= printTestSuite("Create table");
			$output .= beginTestCase();
			
			$table = new TableSchema("test1");
			$table->addColumnDefinition("column1", "varchar", 255);
			$table->addColumnDefinition("column2", "varchar", 255);
			$table->addColumnDefinition("column3", "int");
			$table->addPrimaryKeyDefinition("column1");
			$table->addPrimaryKeyDefinition("column2");
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable test1", OKify($ret));
			
			$table = new TableSchema("test2");
			$table->addColumnDefinition("column1", "varchar", 50);
			$table->addColumnDefinition("column2", "varchar", 100);
			$table->addColumnDefinition("column3", "int");
			$table->addPrimaryKeyDefinition("column1");
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable test2", OKify($ret));
			
			$table = new TableSchema("test3");
			$table->addColumnDefinition("column1", "varchar", 123);
			$table->addColumnDefinition("column2", "varchar", 234);
			$table->addColumnDefinition("column3", "int");
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable test3", OKify($ret));
			
			$output .= endTestCase();	
		}
		
		if (strcmp($module, "all") == 0 || strcmp($module, "get_table") == 0)
		{
			$output .= printTestSuite("Get table test1");
			$output .= beginTestCase();
			
			$table = new TableSchema("test1");
			$table->addColumnDefinition("column1", "varchar", 255);
			$table->addColumnDefinition("column2", "varchar", 255);
			$table->addColumnDefinition("column3", "int");
			$table->addPrimaryKeyDefinition("column1");
			$table->addPrimaryKeyDefinition("column2");
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$table2 = DAL::getTableSchema("test1");
			$output .= print_r($table2, true);
			
			$output .= endTestCase();	
		}
		
		if (strcmp($module, "all") == 0 || strcmp($module, "get_table_version") == 0)
		{
			$output .= printTestSuite("Get table version");
			$output .= beginTestCase();
			
			$table = new TableSchema("test1");
			$table->addColumnDefinition("column1", "varchar", 255);
			$table->addColumnDefinition("column2", "varchar", 255);
			$table->addColumnDefinition("column3", "int");
			$table->addPrimaryKeyDefinition("column1");
			$table->addPrimaryKeyDefinition("column2");
			$table->version = 2;
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$version = DAL::getTableVersion("test1");
			$output .= printTestCase("getTableVersion test1", $version);
			
			$version = DAL::getTableVersion("testtest");
			$output .= printTestCase("getTableVersion testtest", $version);
			
			$output .= endTestCase();	
		}
		
		if (strcmp($module, "all") == 0 || strcmp($module, "table_exist") == 0)
		{
			$output .= printTestSuite("Check table exists");
			$output .= beginTestCase();
			
			$table = new TableSchema("test1");
			$table->addColumnDefinition("column1", "varchar", 255);
			$table->addColumnDefinition("column2", "varchar", 255);
			$table->addColumnDefinition("column3", "int");
			$table->addPrimaryKeyDefinition("column1");
			$table->addPrimaryKeyDefinition("column2");
			$table->version = 2;
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$ret = DAL::isTableExist("test1");
			$output .= printTestCase("isTableExist test1", niceBoolean($ret));
			
			$ret = DAL::isTableExist("testtest");
			$output .= printTestCase("isTableExist testtest", niceBoolean($ret));
			
			$output .= endTestCase();	
		}
		
		if (strcmp($module, "all") == 0 || strcmp($module, "truncate_table") == 0)
		{
			$output .= printTestSuite("Truncate table");
			$output .= beginTestCase();
			
			$table = new TableSchema("test1");
			$table->addColumnDefinition("column1", "varchar", 255);
			$table->addColumnDefinition("column2", "varchar", 255);
			$table->addColumnDefinition("column3", "int");
			$table->addPrimaryKeyDefinition("column1");
			$table->addPrimaryKeyDefinition("column2");
			$table->version = 2;
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$ret = DAL::isTableExist("test1");
			$output .= printTestCase("isTableExist test1", niceBoolean($ret));
			
			$ret = DAL::emptyTable("test1");
			$output .= printTestCase("emptyTable", OKify($ret));
			
			$ret = DAL::isTableExist("test1");
			$output .= printTestCase("isTableExist test1", niceBoolean($ret));
			
			$output .= endTestCase();	
		}
		
		if (strcmp($module, "all") == 0 || strcmp($module, "drop_table") == 0)
		{
			$output .= printTestSuite("Drop table");
			$output .= beginTestCase();
			
			$table = new TableSchema("test1");
			$table->addColumnDefinition("column1", "varchar", 255);
			$table->addColumnDefinition("column2", "varchar", 255);
			$table->addColumnDefinition("column3", "int");
			$table->addPrimaryKeyDefinition("column1");
			$table->addPrimaryKeyDefinition("column2");
			$table->version = 2;
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$ret = DAL::isTableExist("test1");
			$output .= printTestCase("isTableExist test1", niceBoolean($ret));
			
			$ret = DAL::dropTable("test1");
			$output .= printTestCase("dropTable", OKify($ret));
			
			$ret = DAL::isTableExist("test1");
			$output .= printTestCase("isTableExist test1", niceBoolean($ret));
			
			$output .= endTestCase();	
		}
		
		if (strcmp($module, "all") == 0 || strcmp($module, "insert") == 0)
		{
			$output .= printTestSuite("Insert into table");
			$output .= beginTestCase();
			
			$table = new TableSchema("test4");
			$table->addColumnDefinition("aaa", "varchar", 255);
			$table->addColumnDefinition("bbb", "varchar", 255);
			$table->addColumnDefinition("ccc", "int");
			$table->addPrimaryKeyDefinition("aaa");
			$table->version = 2;
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$columns = $table->getColumnNames();
			$output .= print_r($columns, true);
			$output .= "<br/>";
			
			$ret = DAL::emptyTable("test4");
			$output .= printTestCase("emptyTable", OKify($ret));
			
			$ret = DAL::insert("test4", $columns, array("boo", "yeah", 1));
			$output .= printTestCase("insert", OKify($ret));

			$ret = DAL::insert("test4", $columns, array("hello", "world", 123));
			$output .= printTestCase("insert", OKify($ret));
			
			$ret = DAL::insert("test4", $columns, array("georgia", "tech", 567));
			$output .= printTestCase("insert", OKify($ret));

			$output .= endTestCase();	
		}
		
		if (strcmp($module, "all") == 0 || strcmp($module, "upsert") == 0)
		{
			$output .= printTestSuite("Upsert table");
			$output .= beginTestCase();
			
			$table = new TableSchema("test4");
			$table->addColumnDefinition("aaa", "varchar", 255);
			$table->addColumnDefinition("bbb", "varchar", 255);
			$table->addColumnDefinition("ccc", "int");
			$table->addPrimaryKeyDefinition("aaa");
			$table->version = 2;
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$columns = $table->getColumnNames();
			$output .= print_r($columns, true);
			$output .= "<br/>";
			
			$ret = DAL::emptyTable("test4");
			$output .= printTestCase("emptyTable", OKify($ret));
			
			$ret = DAL::upsert("test4", $columns, array("boo", "yeah", 1));
			$output .= printTestCase("upsert", OKify($ret));

			$ret = DAL::upsert("test4", $columns, array("boo", "yeah", 1));
			$output .= printTestCase("upsert", OKify($ret));
			
			$ret = DAL::upsert("test4", $columns, array("boo", "booyeah", 123));
			$output .= printTestCase("upsert", OKify($ret));

			$output .= endTestCase();	
		}
		
		if (strcmp($module, "all") == 0 || strcmp($module, "select") == 0)
		{
			$output .= printTestSuite("Select table");
			$output .= beginTestCase();
			
			$table = new TableSchema("test4");
			$table->addColumnDefinition("aaa", "varchar", 255);
			$table->addColumnDefinition("bbb", "varchar", 255);
			$table->addColumnDefinition("ccc", "int");
			$table->addPrimaryKeyDefinition("aaa");
			$table->version = 2;
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$columns = $table->getColumnNames();
			$output .= print_r($columns, true);
			$output .= "<br/>";
			
			$ret = DAL::emptyTable("test4");
			$output .= printTestCase("emptyTable", OKify($ret));
			
			$ret = DAL::upsert("test4", $columns, array("boo", "yeah", 1));
			$output .= printTestCase("upsert", OKify($ret));

			$ret = DAL::upsert("test4", $columns, array("hello", "world", 123));
			$output .= printTestCase("upsert", OKify($ret));
			
			$ret = DAL::upsert("test4", $columns, array("georgia", "tech", 567));
			$output .= printTestCase("upsert", OKify($ret));
			
			$data = DAL::select("test4");
			$output .= print_r($data, true);
			$output .= "<br/>";
			
			$data = DAL::select("test4", array("aaa", "ccc"));
			$output .= print_r($data, true);
			$output .= "<br/>";
			
			$data = DAL::select("test4", array("aaa", "bbb", "ccc"), array("aaa"), array("boo"));
			$output .= print_r($data, true);
			$output .= "<br/>";

			$data = DAL::select("test4", array("aaa", "ccc"), array("aaa", "ccc"), array("georgia", 567));
			$output .= print_r($data, true);
			$output .= "<br/>";

			$output .= endTestCase();	
		}
		
		if (strcmp($module, "all") == 0 || strcmp($module, "update") == 0)
		{
			$output .= printTestSuite("Update table");
			$output .= beginTestCase();
			
			$table = new TableSchema("test4");
			$table->addColumnDefinition("aaa", "varchar", 255);
			$table->addColumnDefinition("bbb", "varchar", 255);
			$table->addColumnDefinition("ccc", "int");
			$table->addPrimaryKeyDefinition("aaa");
			$table->version = 2;
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$columns = $table->getColumnNames();
			$output .= print_r($columns, true);
			$output .= "<br/>";
			
			$ret = DAL::emptyTable("test4");
			$output .= printTestCase("emptyTable", OKify($ret));
			
			$ret = DAL::insert("test4", $columns, array("boo", "yeah", 1));
			$output .= printTestCase("insert", OKify($ret));
			
			$data = DAL::select("test4");
			$output .= print_r($data, true);
			$output .= "<br/>";
			
			$ret = DAL::update("test4", $columns, array("georgia", "tech", 123), array("aaa"), array("boo"));
			$output .= printTestCase("update", OKify($ret));
			
			$data = DAL::select("test4");
			$output .= print_r($data, true);
			$output .= "<br/>";
			
			$output .= endTestCase();	
		}
		
		DAL::disconnect();
		echo $output;
	}
}

function printTestSuite($title)
{
	$output = "<h1>$title</h1>";
	return $output;
}

function beginTestCase()
{
	$output = "<p>";
	return $output;
}

function endTestCase()
{
	$output = "</p>";
	return $output;
}

function printTestCase($title, $result)
{
	$output = "$title : $result <br/>";
	return $output;
}

function OKify($bool)
{
	if ($bool)
		return "OK";
	else
		return "Error";
}

function niceBoolean($bool)
{
	if ($bool)
		return "true";
	else
		return "false";
}

?>