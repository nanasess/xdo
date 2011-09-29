<?php

/**
 * トランザクションを扱うためのクラス.
 *
 * @package XDO
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_Transaction {

    /** PersistenceManager インスタンス. */
    var $persistenceManager;

    /**
     * PersistenceManager を使用して, インスタンスを生成する.
     *
     * 同時に, 現在のトランザクションを開始する.
     *
     * @param XDO_PersistenceManager &$persistenceManager
     *                                PersistenceManager インスタンス
     */
    function XDO_Transaction(&$persistenceManager) {
        $this->__construct($persistenceManager);
    }

    /**
     * PersistenceManager を使用して, インスタンスを生成する.
     *
     * 同時に, 現在のトランザクションを開始する.
     *
     * @param XDO_PersistenceManager &$persistenceManager 
     *                                PersistenceManager インスタンス
     */
    function __construct(&$persistenceManager) {
        $this->persistenceManager = $persistenceManager;
        $this->persistenceManager->executeBySql("BEGIN");
    }

    /**
     * 現在のトランザクションを開始する.
     *
     * @access public
     * @return void
     */
    function begin() {
        $this->persistenceManager->executeBySql("BEGIN");
    }

    /**
     * 現在のトランザクションをコミットする.
     *
     * @access public
     * @return void
     */
    function commit() {
        $this->persistenceManager->executeBySql("COMMIT");
    }

    /**
     * 現在のトランザクションのロールバックを行う.
     *
     * @access public
     * @return void
     */
    function rollback() {
        $this->persistenceManager->executeBySql("ROLLBACK");
    }
}
?>
