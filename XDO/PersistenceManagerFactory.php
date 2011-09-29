<?php

require_once("ConnectionFactory.php");
require_once('PersistenceManager.php');

/**
 * PersistenceManager のファクトリークラス.
 *
 * データストアへの接続を行い, PersistenceManager のインスタンス生成を管理する.
 *
 * @package XDO
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_PersistenceManagerFactory {

    /** ConnectionFactory インスタンス */
    var $connectionFactory;

    /** PersistenceManager インスタンス */
    var $persistenceManager;

    /**
     * DSN を引数にしてインスタンスを生成するコンストラクタ.
     *
     * このクラスのインスタンスを生成すると同時に, データストアへの接続を開始する.
     *
     * @param string $dsn データソース名
     */
    function XDO_PersistenceManagerFactory($dsn = null, $backend) {
        $this->__constract($dsn, $backend);
    }

    /**
     * DSN を引数にしてインスタンスを生成するコンストラクタ.
     *
     * このクラスのインスタンスを生成すると同時に, データストアへの接続を開始する.
     *
     * @param string $dsn データソース名
     */
    function __construct($dsn = null, $backend) {
        // TODO XDO_ConnectionFactory を使用する
        $this->connectionFactory = new XDO_ConnectionFactory($dsn, $backend);
    }

    /**
     * PersistenceManager インスタンスを取得する.
     *
     * PersistenceManager インスタンスは, シングルトンである.
     *
     * @return XDO_PersistenceManager PersistenceManager インスタンス
     */
    function getPersistenceManager() {
        if ($this->persistenceManager == null) {
            $this->persistenceManager = 
                new XDO_PersistenceManager($this->connectionFactory);
        }
        return $this->persistenceManager;
    }
}
?>