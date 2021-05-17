<?php

require_once("VendingMachine.php");
require_once("vendor/autoload.php");

/*
$vending_machine = new VendingMachine();
$vending_machine->insert(3.40);
echo $vending_machine->choose('A01');
echo "\n";
echo $vending_machine->getChange();
echo "\n";
 */

/*
$vending_machine2 = new VendingMachine();
$vending_machine2->insert(2.10);
echo $vending_machine2->choose('A03');
echo $vending_machine2->getChange();
echo "\n";
echo $vending_machine2->getBalance();
echo "\n";
 */

/*
$vending_machine3 = new VendingMachine();
echo $vending_machine3->choose('A01');
echo "\n";
 */

/*
$vending_machine4 = new VendingMachine();
$vending_machine4->insert(1.00);
echo $vending_machine4->choose('A01');
echo $vending_machine4->getChange();
echo "\n";
echo $vending_machine4->choose('A02');
echo "\n";
echo $vending_machine4->getChange();
echo "\n";
 */

/*
$vending_machine5 = new VendingMachine();
echo $vending_machine5->choose("A05");
echo "\n";
 */

/*
$vending_machine6 = new VendingMachine();
$vending_machine6->insert(6.00);
echo $vending_machine6->choose("A04");
echo "\n";
echo $vending_machine6->choose("A04");
echo "\n";
echo $vending_machine6->getChange();
echo "\n";
 */

/*
$vending_machine7 = new VendingMachine();
$vending_machine7->insert(6.00);
echo $vending_machine7->choose("A04");
echo "\n";
$vending_machine7->insert(6.00);
echo "\n";
echo $vending_machine7->choose("A04");
echo "\n";
echo $vending_machine7->choose("A01");
echo "\n";
echo $vending_machine7->choose("A02");
echo "\n";
echo $vending_machine7->choose("A02");
echo "\n";
echo $vending_machine7->getChange();
echo "\n";
echo $vending_machine7->getBalance();
echo "\n";
 */

/*
$vending_machine8 = new VendingMachine();
$vending_machine8->insert(1000.00);

$vending_machine8->setTime("2020-01-01T20:30:00");
echo $vending_machine8->choose("A01");
echo "\n";

$vending_machine8->setTime("2020-03-01T23:30:00");
echo $vending_machine8->choose("A01");
echo "\n";

$vending_machine8->setTime("2020-03-04T09:22:00");
echo $vending_machine8->choose("A01");
echo "\n";

$vending_machine8->setTime("2020-04-01T23:00:00");
echo $vending_machine8->choose("A01");
echo "\n";

$vending_machine8->setTime("2020-04-01T23:59:59");
echo $vending_machine8->choose("A01");
echo "\n";

$vending_machine8->setTime("2020-04-04T09:12:00");
echo $vending_machine8->choose("A01");
echo "\n";

echo $vending_machine8->getSalesReport();
echo "\n";
 */

// Create the Connection URI
// See more: https://github.com/byjg/anydataset#connection-based-on-uri
//$connectionUri = new \ByJG\Util\Uri('mysql://migrateuser:migratepwd@localhost/migratedatabase');
//$connectionUri = new \ByJG\Util\Uri('mysql://cpnv:cpnvpw@localhost/cpnv');
$connectionUri = new \ByJG\Util\Uri('mysql://root:totem@localhost/cpnv');

// Create the Migration instance
$migration = new \ByJG\DbMigration\Migration($connectionUri, '.');

// Register the Database or Databases can handle that URI:
$migration->registerDatabase('mysql', \ByJG\DbMigration\Database\MySqlDatabase::class);
$migration->registerDatabase('maria', \ByJG\DbMigration\Database\MySqlDatabase::class);

// Add a callback progress function to receive info from the execution
$migration->addCallbackProgress(function ($action, $currentVersion, $fileInfo) {
    echo "$action, $currentVersion, ${fileInfo['description']}\n";
});

// Restore the database using the "base.sql" script
// and run ALL existing scripts for up the database version to the latest version
$migration->reset();
?>
