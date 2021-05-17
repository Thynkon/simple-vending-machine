<?php

namespace CPNV;

require_once('DatabaseConnection.php');
require_once('Article.php');
require_once('Sale.php');

Class VendingMachine {
	// database connection
	private $db_connection = null;
	private $db_statement = null;

	private $article = null;
	private $credit = 0;
	private $balance = 0;
	private $change = 0;

	// sale's datetime formatted like '2020-01-01T20:30:00'
	private $datetime = null;

	// sale object that will allow us to get sales report
	private $sale = null;

	function __construct() {
		if (($this->db_connection = \getDbConnection()) === false) {
			echo "Failed to connect to database!\n";
			return false;
		}

		// set default timestamp to now
		$this->datetime = \date('Y-m-d\TH:i:s');
		$this->article = new Article();
		$this->sale = new Sale();
	}

	public function getChange() {
		return $this->change;
	}

	public function getBalance() {
		return $this->balance;
	}

	public function setTime($datetime = null) {
		$this->datetime = $datetime;
	}

	public function insert($amount = null) {
		$this->credit += $amount;
	}

	function choose($code = null) {
		if ($code === null) {
			echo "Missing options!\n";
			return false;
		}

		$this->article->fetchArticleByCode($code);
		if ($this->article->getId() === null) {
			return "Invalid selection!";
		}

		if ($this->article->getQuantity() == 0) {
			return sprintf("Item %s: Out of stock!", $this->article->getName());
		}

		$balance = $this->credit - $this->article->getPrice();
		// if user has sufficient credit
		if ($balance >= 0) {
			// if user hasn't called setTime(), set timestamp to now
			if ($this->datetime === null) {
				$this->datetime = \date('Y-m-d\TH:i:s');
			}

			if (!$this->sale->sellArticle($this->article->getId(), $this->datetime)) {
				return false;
			}

			// remove 1 item from stock
			$new_quantity = $this->article->getQuantity() - 1;
			if ($this->article->updateQuantity($new_quantity) === false) {
				return false;
			}

			// recalculate credit, balance and change
			$this->credit -= $this->article->getPrice();
			$this->balance += $this->article->getPrice();
			$this->change = $balance;

			return sprintf("Vending %s", $this->article->getName());
		} else {
			// give the user his money back
			$this->change = $this->credit;
			return "Not enough money!";
		}

		// paranoia
		return true;
	}

	public function getSalesReport() {
		// get best sales per hour
		$sales = $this->sale->getBestSales();

		if (count($sales) === 0) {
			return "No articles were sold!\n";
		}

		$output = "";
		foreach ($sales as $sale) {
			$output .= sprintf("Hour\t%2d generated a revenue of %.2f\n", $sale["best_hour"], $sale["total"]);
		}

		return $output;
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
