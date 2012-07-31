<?php

// {{{ required
require_once('PersistenceManagerFactory.php');

/**
 * XDO を扱うためのヘルパークラス.
 *
 * @package XDO
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_Helper {

    /**
     * DSN を引数にして, PersistenceManagerFactory を取得する.
     *
     * この関数は static アクセスで使用すること.
     *
     * @param string $dsn データソース名
     * @return XDO_PersistenceManagerFactory PersistenceManagerFactoryインスタンス
     */
    static function getPersistenceManagerFactory($dsn, $backend) {
        return new XDO_PersistenceManagerFactory($dsn, $backend);
    }
}
?>