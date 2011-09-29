<?php

require_once("adodb/adodb.inc.php");

class XDO_ConnectionFactory_ADODB {

    var $dbh;
    function __construct($dsn) {
        $this->dbh = ADONewConnection($dsn);
        $this->dbh->SetFetchMode(ADODB_FETCH_ASSOC);
    }

    /**
     * 任意の更新系 SQL を実行する.
     *
     * @param string $sql 更新系 SQL 文
     * @param array $params 更新系 SQL のパラメータ
     * @return 更新系 SQL の実行結果
     */
    function executeBySql($sql, $params = array()) {
        return $this->dbh->Execute($sql, $params);
    }

    /**
     * 任意の参照系 SQL を実行する.
     *
     * @param string $sql 参照系 SQL 文
     * @param array $params 参照系 SQL のパラメータ
     * @return 参照系 SQL の実行結果
     */
    function query($sql, $params = array()) {
        return $this->dbh->query($sql, $params);
    }

    /**
     * 参照系 SQL を実行し, すべての結果を連想配列で返す.
     *
     * @param string $sql 参照系 SQL 文
     * @param array $params 参照系 SQL のパラメータ
     * @return 参照系 SQL の実行結果の連想配列
     */
    function getAll($sql, $params = array()) {
        return $this->dbh->getAll($sql, $params);
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
        switch ($executeType) {
        case "INSERT":
              return $this->dbh->AutoExecute($tableName, $columns, 'INSERT');
            break;

        default:
        case "UPDATE":
            return $this->dbh->AutoExecute($tableName, $columns, 'UPDATE', $where);
        }
    }

    /**
     * 次のシーケンス値を取得する.
     *
     * @param string $sequenceName シーケンス名
     * @return long 次のシーケンス値
     */
    function nextSequence($sequenceName) {
        return $this->dbh->GenID($sequenceName);
    }

    /**
     * シーケンス値を設定する.
     *
     * @param string $sequenceName シーケンス名
     * @param long $value 設定するシーケンス値
     * @return 実行結果
     */
    function setSequence($sequenceName, $value) {
        return $this->dbh->GenID($sequenceName, $value);
    }
}
?>