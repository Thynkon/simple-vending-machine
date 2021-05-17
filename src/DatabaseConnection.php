<?php

// simple function that returns a database connection
// it also sets database session's timezone to UTC so it is easier
// to manipulate timestamps (if we want to do some complex stuff with dates)
function getDbConnection() {
	$db_host = "localhost";
	$db_username = "root";
	$db_password = "totem";
	$db_name = "cpnv";

	$db_connection = null;
	try {
		if (defined("MYSQL_CONN_ERROR") === false) {
			define("MYSQL_CONN_ERROR", "Unable to connect to database.");
		}
		// Ensure reporting is setup correctly
		// Throw mysqli_sql_exception for errors instead of warnings.
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

		$db_connection = new \mysqli($db_host, $db_username, $db_password, $db_name);
		$db_connection->query('SET time_zone = UTC;');
	} catch(\mysqli_sql_exception $e) {
		echo $e->getMessage();
		return false;
	}

	return $db_connection;
}
?>
