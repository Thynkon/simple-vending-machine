<?php
declare(strict_types=1);

namespace UnitTestFiles\Test;
use PHPUnit\Framework\TestCase;
use CPNV\VendingMachine;

define("PROJECT_ROOT", dirname(__FILE__) . '/../');
require_once(PROJECT_ROOT . 'vendor/autoload.php');
require_once(PROJECT_ROOT . 'src/VendingMachine.php');

class VendingMachineTest extends TestCase {
    protected $vending_machine;
    protected $migration;

    public function setUp(): void {
    	$this->vending_machine = new VendingMachine();

	$connectionUri = new \ByJG\Util\Uri('mysql://root:totem@localhost/cpnv');

	// Create the Migration instance
	$this->migration = new \ByJG\DbMigration\Migration($connectionUri, '.');

	// Register the Database or Databases can handle that URI:
	$this->migration->registerDatabase('mysql', \ByJG\DbMigration\Database\MySqlDatabase::class);
	$this->migration->registerDatabase('maria', \ByJG\DbMigration\Database\MySqlDatabase::class);

	// Add a callback progress function to receive info from the execution
	$this->migration->addCallbackProgress(function ($action, $currentVersion, $fileInfo) {
	    echo "$action, $currentVersion, ${fileInfo['description']}\n";
	});
    }

    // Les 7 premiers tests concernent la version simplifiée du distributeur automatique
    public function test1() {
	// reset database
	$this->migration->reset();
	// run version 1 script
	$this->migration->update(1);

	// setting date for sale so tests won't fail
	$this->vending_machine->setTime('2020-01-01T20:30:00');
	$this->vending_machine->insert(3.40);
	$this->assertEquals(
		"Vending Smarlies",
		$this->vending_machine->choose('A01')
	);

	$this->assertEquals(
		1.80,
		$this->vending_machine->getChange()
	);
    }

    public function test2() {
	// reset database
	$this->migration->reset();
	// run version 1 script
	$this->migration->update(1);

	// setting date for sale so tests won't fail
	$this->vending_machine->setTime('2020-01-01T20:31:00');
	$this->vending_machine->insert(2.10);

	$this->assertEquals("Vending Avril", $this->vending_machine->choose('A03'));
	$this->assertEquals(0.00, $this->vending_machine->getChange());
	$this->assertEquals(2.10, $this->vending_machine->getBalance());
    }

    public function test3() {
	// reset database
	$this->migration->reset();
	// run version 1 script
	$this->migration->update(1);

	// setting date for sale so tests won't fail
	$this->vending_machine->setTime('2020-01-01T20:33:00');
	$this->assertEquals("Not enough money!", $this->vending_machine->choose('A01'));
    }

    public function test4() {
	// reset database
	$this->migration->reset();
	// run version 1 script
	$this->migration->update(1);

	// setting date for sale so tests won't fail
	$this->vending_machine->setTime('2020-01-01T20:34:00');
	$this->vending_machine->insert(1.00);

	$this->assertEquals("Not enough money!", $this->vending_machine->choose('A01'));
	$this->assertEquals(1.00, $this->vending_machine->getChange());
	$this->assertEquals("Vending Carampar", $this->vending_machine->choose('A02'));
	$this->assertEquals(0.40, $this->vending_machine->getChange());
    }

    public function test5() {
	// reset database
	$this->migration->reset();
	// run version 1 script
	$this->migration->update(1);

	// setting date for sale so tests won't fail
	$this->vending_machine->setTime('2020-01-01T20:35:00');
	$this->vending_machine->insert(1.00);

	$this->assertEquals("Invalid selection!", $this->vending_machine->choose('A05'));
    }

    public function test6() {
	// reset database
	$this->migration->reset();
	// run version 1 script
	$this->migration->update(1);

	// setting date for sale so tests won't fail
	$this->vending_machine->setTime('2020-01-01T20:36:00');
	$this->vending_machine->insert(6.00);

	$this->assertEquals("Vending KokoKola", $this->vending_machine->choose('A04'));
	$this->assertEquals("Item KokoKola: Out of stock!", $this->vending_machine->choose('A04'));
	$this->assertEquals(3.05, $this->vending_machine->getChange());
    }

    public function test7() {
	// reset database
	$this->migration->reset();
	// run version 1 script
	$this->migration->update(1);

	// setting date for sale so tests won't fail
	$this->vending_machine->setTime('2020-01-01T20:37:00');
	$this->vending_machine->insert(6.00);

	$this->assertEquals("Vending KokoKola", $this->vending_machine->choose('A04'));

	$this->vending_machine->insert(6.00);

	$this->assertEquals("Item KokoKola: Out of stock!", $this->vending_machine->choose('A04'));
	$this->assertEquals("Vending Smarlies", $this->vending_machine->choose('A01'));
	$this->assertEquals("Vending Carampar", $this->vending_machine->choose('A02'));
	$this->assertEquals("Vending Carampar", $this->vending_machine->choose('A02'));
	$this->assertEquals(6.25, $this->vending_machine->getChange());
	$this->assertEquals(5.75, $this->vending_machine->getBalance());
    }

    public function test8() {
	// reset database
	$this->migration->reset();
	// run version 2 script (updated articles)
	$this->migration->update(2);

	$this->vending_machine->insert(1000.00);
	// setting date for sale
	$this->vending_machine->setTime("2020-01-01T20:30:00");
	$this->vending_machine->choose('A01');

	// setting date for sale
	$this->vending_machine->setTime("2020-03-01T23:30:00");
	$this->vending_machine->choose('A01');

	// setting date for sale
	$this->vending_machine->setTime("2020-03-04T09:22:00");
	$this->vending_machine->choose('A01');

	// setting date for sale
	$this->vending_machine->setTime("2020-04-01T23:00:00");
	$this->vending_machine->choose('A01');

	// setting date for sale
	$this->vending_machine->setTime("2020-04-01T23:59:59");
	$this->vending_machine->choose('A01');

	// setting date for sale
	$this->vending_machine->setTime("2020-04-04T09:12:00");
	$this->vending_machine->choose('A01');


	$this->assertEquals(
		"Hour\t23 generated a revenue of 4.80\nHour\t 9 generated a revenue of 3.20\nHour\t20 generated a revenue of 1.60\n",
		$this->vending_machine->getSalesReport()
	);
    }
}

