<?php

define("XDO_BACKEND_ADODB", "ADODB");
define("XDO_BACKEND_MDB2", "MDB2");


/**
 * データストアとの接続ドライバを抽象化するためのファクトリークラス.
 *
 * 主に, データストアへの接続ドライバの抽象化を行う.
 * 今のところ, ADOdb に対応している.
 * 将来的には, PEAR::MDB2, PDO などのラッパーとして動作する.
 *
 * @package XDO
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_ConnectionFactory {

    /** backend class instance. */
    var $instance;

    /**
     * DSN を使用して, インスタンスを生成するコンストラクタ.
     *
     * @access public
     * @param string $dsn データソース名
     */
    function XDO_ConnectionFactory($dsn, $backend) {
        $this->__construct($dsn, $backend);
    }

    /**
     * DSN を使用して, インスタンスを生成するコンストラクタ.
     *
     * @access public
     * @param string $dsn データソース名
     */
    function __construct($dsn, $backend) {
        include_once("ConnectionFactory/" . $backend  . ".php");
        $backendClass = "XDO_ConnectionFactory_" . $backend;
        $this->instance = new $backendClass($dsn);
    }

    /**
     * 任意の更新系 SQL を実行する.
     *
     * @param string $sql 更新系 SQL 文
     * @param array $params 更新系 SQL のパラメータ
     * @return 更新系 SQL の実行結果
     */
    function executeBySql($sql, $params = array()) {
        return $this->instance->executeBySql($sql, $params);
    }

    /**
     * 任意の参照系 SQL を実行する.
     *
     * @param string $sql 参照系 SQL 文
     * @param array $params 参照系 SQL のパラメータ
     * @return 参照系 SQL の実行結果
     */
    function query($sql, $params = array()) {
        return $this->instance->query($sql, $params);
    }

    /**
     * 参照系 SQL を実行し, すべての結果を連想配列で返す.
     *
     * @param string $sql 参照系 SQL 文
     * @param array $params 参照系 SQL のパラメータ
     * @return 参照系 SQL の実行結果の連想配列
     */
    function getAll($sql, $params = array()) {
        return $this->instance->getAll($sql, $params);
    }

    /**
     * INSERT または UPDATE の SQL を実行する.
     *
     * @param string $tableName テーブル名
     * @param array $columns カラム名の配列
     * @param string $executeType INSERT または UPDATE
     * @param string $where UPDATE の場合の WHERE 句
     * @return INSERT または UPDATE の実行結果
     */
    function autoExecute($tableName, $columns, $executeType, $where = "") {
        return $this->instance->autoExecute($tableName, $columns, $executeType, $where);
    }

    /**
     * 次のシーケンス値を取得する.
     *
     * @param string $sequenceName シーケンス名
     * @return long 次のシーケンス値
     */
    function nextSequence($sequenceName) {
        return $this->instance->nextSequence($sequenceName);
    }

    /**
     * シーケンス値を設定する.
     *
     * @param string $sequenceName シーケンス名
     * @param long $value 設定するシーケンス値
     * @return 実行結果
     */
    function setSequence($sequenceName, $value) {
        return $this->instance->setSequence($sequenceName, $value);
    }
}
?>
