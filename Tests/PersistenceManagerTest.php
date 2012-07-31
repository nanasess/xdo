<?php
// {{{ requires

// FIXME 暫定措置
error_reporting(E_ALL);

require_once("XDO/Helper.php");
require_once("XDO/Entity/Abstract.php");
//require_once("PHPUnit/Framework.php");

/**
 * XDO_PersistenceManager のテストケース.
 *
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_PresistenceManagerTest extends PHPUnit_Framework_TestCase {

    var $expected;
    var $actual;
    var $pmf;
    var $entity;
    var $tx;

    function setUp() {
        $this->pmf = XDO_Helper::getPersistenceManagerFactory('pgsql://postgres:pass@localhost/xdo_test', XDO_BACKEND_MDB2);
        $pm = $this->pmf->getPersistenceManager();
        $this->tx = $pm->currentTransaction();
        $this->tx->begin();
        $this->entity = new XDO_Entity_Test2();
    }

    function tearDown() {
        // $this->tx->rollback();
    }

    function verify() {
        $this->assertEquals($this->expected, $this->actual);
    }

    function testGetInstance() {
        $pm = $this->pmf->getPersistenceManager();
 
        $this->expected = true;
        $this->actual = is_object($pm);

        $this->verify();
    }

    function testMakePersistent() {
        $this->createTestTable();

        $this->entity->id = 1;
        $this->entity->column1 = "1";
        $this->entity->column2 = "2";
        $this->entity->column3 = "f";

        $this->expected = $this->entity;

        $pm = $this->pmf->getPersistenceManager();
        $this->actual = $pm->makePersistent($this->entity);

        $this->verify();
    }

    function testMakePersistentForUpdate() {
        $this->createTestTable();
        $this->setTestData(1, 1, 1, "f");

        $this->entity->id = 1;
        $this->entity->column1 = "1";
        $this->entity->column2 = "1";
        $this->entity->column3 = "f";

        $this->expected = $this->entity;

        $pm = $this->pmf->getPersistenceManager();
        $this->actual = $pm->makePersistent($this->entity);

        $this->verify();
    }

    function testMakePersistentForInsert() {
        $this->createTestTable();

        $this->entity->id = 2;
        $this->entity->column1 = "2";
        $this->entity->column2 = "2";
        $this->entity->column3 = "f";

        $this->expected = $this->entity;

        $pm = $this->pmf->getPersistenceManager();
        $this->actual = $pm->makePersistent($this->entity);

        $this->verify();
    }


    /*
    function testGetObjectById() {
        $this->createTestTable();
        $this->setTestData(1, 1, 1, "f");

        $this->entity->id = 1;
        $this->entity->column1 = 1;
        $this->entity->column2 = 1;
        $this->entity->column3 = "f";

        $pm = $this->pmf->getPersistenceManager();

        $this->expected = $this->entity;
        $this->actual = $pm->getObjectById(new XDO_Entity_Test2(), 1);

        $this->verify();
    }
    */

    /*
    function testGetObjectsByParam() {
        $this->createTestTable();
        $this->setTestData(1, 1, 1, "f");
        $this->setTestData(2, 2, 1, "f");

        $pm = $this->pmf->getPersistenceManager();

        $entities = array(new XDO_Entity_Test2(), new XDO_Entity_Test2());
        $entities[0]->id = 1;
        $entities[0]->column1 = 1;
        $entities[0]->column2 = 1;
        $entities[0]->column3 = "f";
        $entities[1]->id = 2;
        $entities[1]->column1 = 2;
        $entities[1]->column2 = 1;
        $entities[1]->column3 = "f";

        $results = $pm->getObjectsByParam(new XDO_Entity_Test2(),
                                          array("column2" => 1));


        $this->expected = array($entities[0]->toString(),
                                $entities[1]->toString());
        $this->actual = array($results[0]->toString(),
                              $results[1]->toString());

        $this->verify();
    }
    */

    function testDeletePersistent() {
        $this->createTestTable();
        $this->setTestData(1, 1, 1, "f");
        $this->entity->setProperty("id", 1);

        $pm = $this->pmf->getPersistenceManager();
        $pm->deletePersistent($this->entity);

        $this->expected = null;
        $this->actual = $pm->getObjectById($entity, 1);

        $this->verify();
    }
    /*
    function testGetSequence() {
        $pm = $this->pmf->getPersistenceManager();
        $pm->executeBySql("CREATE SEQUENCE test_sequence_seq");
        $sequence = $pm->getSequence("test_sequence_seq");

        $this->expected = 1;
        $this->actual = $sequence->nextValue();
        
        $this->verify();

    }
    */
    function createTestTable() {
        $sql = "CREATE TEMPORARY TABLE test_table ("
            . "id int PRIMARY KEY,"
            . "column1 numeric(9),"
            . "column2 varchar(20),"
            . "column3 char(1)"
            . ")";
        

        return $this->pmf->connectionFactory->executeBySql($sql);
    }

    function dropTestTable() {
        $sql = "DROP TABLE test_table";
        return $this->objDbConn->query($sql);
    }

    function setTestData($id, $column1, $column2, $column3) {
        $fields_values = array("id" => $id,
                               "column1" => $column1,
                               "column2" => $column2,
                               "column3" => $column3);
        return $this->pmf->connectionFactory->AutoExecute("test_table", $fields_values, 'INSERT');
    }
}

class XDO_Entity_Test2 extends XDO_Entity_Abstract {

    var $id;
    var $column1;
    var $column2;
    var $column3;

    function XDO_Entity_Test2() {
        $this->__construct();
    }

    function __construct() {
        $this->tableName = "test_table";
        $this->columnNames = array("id", "column1", "column2", "column3");
        $this->primaryKeys = array("id");
    }
}
