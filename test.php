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
		
		if (strcmp($module, "all") == 0 || strcmp($module, "create table") == 0)
		{
			$output .= printTestSuite("Create table test1");
			$output .= beginTestCase();
			
			$table = new TableSchema("test1");
			$table->addColumnDefinition("column1", "varchar", 255);
			$table->addColumnDefinition("column2", "varchar", 255);
			$table->addColumnDefinition("column3", "int");
			$table->addPrimaryKeyDefinition("column1");
			$table->addPrimaryKeyDefinition("column2");
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$table = new TableSchema("test2");
			$table->addColumnDefinition("column1", "varchar", 50);
			$table->addColumnDefinition("column2", "varchar", 100);
			$table->addColumnDefinition("column3", "int");
			$table->addPrimaryKeyDefinition("column1");
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
			$table = new TableSchema("test3");
			$table->addColumnDefinition("column1", "varchar", 123);
			$table->addColumnDefinition("column2", "varchar", 234);
			$table->addColumnDefinition("column3", "int");
			$ret = DAL::createTable($table);
			$output .= printTestCase("createTable", OKify($ret));
			
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