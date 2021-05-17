<?php

namespace CPNV;

// class representing an article's sale
// useful for compiling sales statistics
Class Sale {
	// database connection
	private $db_connection = null;
	private $db_statement = null;

	private $id = null;
	private $article = null;
	private $timestamp = 0;
	function __construct() {
		if (($this->db_connection = \getDbConnection()) === false) {
			echo "Failed to connect to database!\n";
			return false;
		}

		$this->article = new Article();
	}

	public function sellArticle($article_id = null, $datetime = null) {
		if ($article_id === null || $datetime === null) {
			echo "Missing options!\n";
			return false;
		}

		$query = "";
		$status = false;

		$query = "INSERT INTO `sale`(article_id, timestamp) ";
		$query .= "VALUES (?, ?);";

		if (($this->db_connection = \getDbConnection()) === false) {
			echo "Failed to connect to database!\n";
			return false;
		}

		if ($this->db_statement = $this->db_connection->prepare($query)) {
			$this->db_statement->bind_param('ds', $article_id, $datetime);

			if ($this->db_statement->execute() === false) {
				echo "Failed to add sale!\n";
			}

			if ($this->db_statement->affected_rows < 1) {
				echo "Article was not sold!\n";
			} else {
				echo "Article was successfully sold!\n";
				$status = true;
			}

			// close statement
			$this->db_statement->close();
			$this->db_statement = null;
		}

		// close db connection
		$this->db_connection->close();
		$this->db_connection = null;

		return $status;
	}

	public function getBestSales() {
		$query = "";
		$result = false;

		// get the 3 best sales revenues per hour
		$query = "SELECT hour(`sale`.timestamp) AS best_hour, SUM(`article`.price) AS total ";
		$query .= "FROM `sale` ";
		$query .= "INNER JOIN `article` ON `article`.id = `sale`.article_id ";
		$query .= "GROUP BY HOUR(`sale`.timestamp) ";
		$query .= "ORDER BY `total` DESC ";
		$query .= "LIMIT 3;";

		$best_sales = array();

		if (($this->db_connection = \getDbConnection()) === false) {
			echo "Failed to connect to database!\n";
			return false;
		}

		if ($this->db_statement = $this->db_connection->prepare($query)) {
			$this->db_statement->execute();

			$result = $this->db_statement->get_result();

			while($row = $result->fetch_assoc()) {
				array_push($best_sales, $row);
			}

			// close statement
			$this->db_statement->close();
			$this->db_statement = null;
		}

		// close db connection
		$this->db_connection->close();
		$this->db_connection = null;

		return $best_sales;
	}

	// free memory
	function __destruct() {
		if ($this->db_statement !== null) {
			$this->db_statement->close();
			$this->db_statement = null;
		}
		if ($this->db_connection !== null) {
			$this->db_connection->close();
			$this->db_connection = null;
		}
	}
}

?>
