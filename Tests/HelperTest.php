<?php

// {{{ requires
require_once("XDO/Helper.php");
require_once("PHPUnit/Framework.php");

/**
 * XDO_Helper のテストケース.
 *
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_HelperTest extends PHPUnit_Framework_TestCase {

    var $expected;
    var $actual;

    function setUp() {
    }

    function tearDown() {
    }

    function verify() {
        $this->assertEquals($this->expected, $this->actual);
    }

    function testGetPersistenceManagerFactory() {
        $pmf = XDO_Helper::getPersistenceManagerFactory('pgsql://postgres:pass@localhost/xdo_test', XDO_BACKEND_MDB2);
 
        $this->expected = true;
        $this->actual = is_object($pmf);

        $this->verify();
    }

    function testGetPersistenceManager() {

        $pmf = XDO_Helper::getPersistenceManagerFactory('pgsql://postgres:pass@localhost/xdo_test', XDO_BACKEND_MDB2);
        $pm = $pmf->getPersistenceManager();
        $this->expected = true;
        $this->actual = is_object($pm);

        $this->verify();
    }
}