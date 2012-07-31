<?php

// {{{ requires
require_once("XDO/Entity/Abstract.php");
//require_once("PHPUnit/Framework.php");

/**
 * LC_Entity_Abstract のテストケース.
 *
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_Entity_AbstractTest extends PHPUnit_Framework_TestCase {
    
    var $expected;
    var $actual;

    function verify() {
        $this->assertEquals($this->expected, $this->actual);
    }

    function testGetInstance() {

        $test = new XDO_Entity_Test();

        $this->expected = true;
        $this->actual = is_object($test);

        $this->verify();
    }

    function testGetColumnNames() {
        $test = new XDO_Entity_Test();

        $this->expected = array("column_one", "column_two", "column_three");
        $this->actual = $test->getColumnNames();

        $this->verify();
    }

    function testGetTableName() {
        $test = new XDO_Entity_Test();

        $this->expected = "test";
        $this->actual = $test->getTableName();

        $this->verify();
    }

    function testGetProperty() {
        $test = new XDO_Entity_Test();
        $test->column_one = "test_data1";

        $this->expected = "test_data1";
        $this->actual = $test->getProperty("column_one");

        $this->verify();
    }

    function testSetProperty() {
        $test = new XDO_Entity_Test();

        $this->expected = "test_data1";
        $test->setProperty("column_one", $this->expected);
        $this->actual = $test->column_one;

        $this->verify();
    }

    function testReadOnly() {
        $test = new XDO_Entity_Test();
        $test->setReadOnly(true);

        $this->expected = true;
        $this->actual = $test->isReadOnly();

        $this->verify();
    }

    function testGetClassName() {
        $test = new XDO_Entity_Test();

        $this->expected = "XDO_Entity_Test";
        $this->actual = $test->getClassName();

        $this->verify();
    }

    function testSetPropertyAndGetProperty() {
        $test = new XDO_Entity_Test();

        $this->expected = "test_data1";
        $test->setProperty("column_one", $this->expected);
        $this->actual = $test->getProperty("column_one");

        $this->verify();
    }

    function testToArray() {
        $test = new XDO_Entity_Test();

        $this->expected = array("column_one" => "test_one",
                                "column_two" => "test_two",
                                "column_three" => "test_three");

        $test->setProperty("column_one", "test_one")
             ->setProperty("column_two", "test_two")
             ->setProperty("column_three", "test_three");

        $this->actual = $test->toArray();

        $this->verify();
    }

    function testToString() {
        $test = new XDO_Entity_Test();

        $this->expected = "[column_one]=>test_one,[column_two]=>test_two,[column_three]=>test_three,";

        $test->setProperty("column_one", "test_one")
             ->setProperty("column_two", "test_two")
             ->setProperty("column_three", "test_three");

        $this->actual = $test->toString();

        $this->verify();

    }
}

class XDO_Entity_Test extends XDO_Entity_Abstract {

    var $column_one;
    var $column_two;
    var $column_three;

    function XDO_Entity_Test() {
        $this->__construct();
    }

    function __construct() {
        $this->tableName = "test";
        $this->columnNames = array("column_one", "column_two", "column_three");
        $this->primaryKeys = array("column_one");
    }
}

?>