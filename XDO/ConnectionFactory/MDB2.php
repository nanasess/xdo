<?php

require_once("MDB2.php");

class XDO_ConnectionFactory_MDB2 {

    var $dbh;
    function __construct($dsn) {
        $this->dbh =& MDB2::factory($dsn);
        if (PEAR::isError($this->dbh)) {
            die($this->dbh->getMessage());
        }
        $this->dbh->setFetchMode(MDB2_FETCHMODE_ASSOC);
    }

    /**
     * 任意の更新系 SQL を実行する.
     *
     * @param string $sql 更新系 SQL 文
     * @param array $params 更新系 SQL のパラメータ
     * @return 更新系 SQL の実行結果
     */
    function executeBySql($sql, $params = array()) {
        $sth = $this->dbh->prepare($sql);
        return $sth->execute(array_values($params));
    }

    /**
     * 任意の参照系 SQL を実行する.
     *
     * @param string $sql 参照系 SQL 文
     * @param array $params 参照系 SQL のパラメータ
     * @return 参照系 SQL の実行結果
     */
    function query($sql, $params = array()) {
        $sth = $this->dbh->prepare($sql);
        return $sth->execute($params);
    }

    /**
     * 参照系 SQL を実行し, すべての結果を連想配列で返す.
     *
     * @param string $sql 参照系 SQL 文
     * @param array $params 参照系 SQL のパラメータ
     * @return 参照系 SQL の実行結果の連想配列
     */
    function getAll($sql, $params = array()) {
        $resultCommon = $this->query($sql, array_values($params));
        return $resultCommon->fetchAll();
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
        $this->dbh->loadModule('Extended');
        switch ($executeType) {
        case "INSERT":
              return $this->dbh->extended->autoExecute($tableName, $columns,
                                                       MDB2_AUTOQUERY_INSERT);
            break;

        default:
        case "UPDATE":
            return $this->dbh->extended->AutoExecute($tableName, $columns,
                                                     MDB2_AUTOQUERY_UPDATE,
                                                     $where);
        }
    }

    /**
     * 次のシーケンス値を取得する.
     *
     * @param string $sequenceName シーケンス名
     * @return long 次のシーケンス値
     */
    function nextSequence($sequenceName) {
        return $this->dbh->nextID($sequenceName);
    }

    /**
     * シーケンス値を設定する.
     *
     * @param string $sequenceName シーケンス名
     * @param long $value 設定するシーケンス値
     * @return 実行結果
     */
    function setSequence($sequenceName, $value) {
        return $this->dbh->createSequence($sequenceName, $value);
    }
}
?>
