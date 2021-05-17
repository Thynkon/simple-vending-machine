<?php
namespace CPNV;

// class representing a vending machine's article
Class Article {
	private $db_connection = null;
	private $db_statement = null;

	private $id = null;
	private $name = null;
	private $code = null;
	private $quantity = 0;
	private $price = 0;

	function __construct($name = null, $code = null, $quantity = null, $price = null) {
		if (($this->db_connection = \getDbConnection()) === false) {
			echo "Failed to connect to database!\n";
			return false;
		}

		if ($name !== null) {
			$this->name = $name;
		}

		if ($code !== null) {
			$this->code = $code;
		}

		if ($quantity !== null) {
			$this->quantity = $quantity;
		}

		if ($price !== null) {
			$this->price = $price;
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getQuantity() {
		return $this->quantity;
	}

	public function getName() {
		return $this->name;
	}

	public function getPrice() {
		return $this->price;
	}

	// get article by it's selection code (ie. A01, A02, etc...)
	public function fetchArticleByCode($code = null) {
		if ($code === null) {
			echo "Missing options!\n";
			return false;
		}

		$query = "";
		$result = false;

		$query = "SELECT * ";
		$query .= "FROM `article` ";
		$query .= "WHERE code = ?;";

		if (($this->db_connection = \getDbConnection()) === false) {
			echo "Failed to connect to database!\n";
			return false;
		}

		if ($this->db_statement = $this->db_connection->prepare($query)) {
			$this->db_statement->bind_param('s', $code);
			$this->db_statement->execute();

			$result = $this->db_statement->get_result();

			// fetch data from database
			while($row = $result->fetch_assoc()) {
				$this->id = $row["id"];
				$this->name = $row["name"];
				$this->code = $row["code"];
				$this->quantity = $row["quantity"];
				$this->price = $row["price"];
			}

			// close statement
			$this->db_statement->close();
			$this->db_statement = null;
		}

		// close db connection
		$this->db_connection->close();
		$this->db_connection = null;
	}

	public function updateQuantity($quantity = null) {
		if ($quantity === null) {
			echo "Missing options!\n";
			return false;
		}

		$query = "";
		$status = false;

		$query = "UPDATE `article` ";
		$query .= "SET quantity = ? ";
		$query .= "WHERE id = ?;";

		if (($this->db_connection = \getDbConnection()) === false) {
			echo "Failed to connect to database!\n";
			return false;
		}

		if ($this->db_statement = $this->db_connection->prepare($query)) {
			$this->db_statement->bind_param('dd', $quantity, $this->id);

			if ($this->db_statement->execute() === false) {
				echo "Failed to update article's quantity!\n";
			}

			if ($this->db_statement->affected_rows < 1) {
				echo "Article was not updated!\n";
			} else {
				echo "Article was successfully updated!\n";
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
