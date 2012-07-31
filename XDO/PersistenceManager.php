<?php
// {{{ requires
require_once('Transaction.php');
require_once('Sequence.php');

/**
 * エンティティを永続化するためのクラス.
 *
 * このクラスは, XDO_Entity_Abstract クラスを継承した, エンティティクラス
 * の永続化をサポートする.
 *
 * @package XDO
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_PersistenceManager {

    //  TODO SQL を自動生成する部分は, Entity (or EntityManager) に任せる.

    /** ConnectionFactory インスタンス. */
    var $connectionFactory;
    /** Transaction インスタンス */
    var $transaction;
    /** データストアとの接続が閉じているかどうか. */
    var $closed;

    /**
     * ConnectionFactory を使用して, インスタンスを生成するコンストラクタ.
     *
     * @access public
     * @param ConnectionFactory $connectionFactory ConnectionFactory インスタンス
     */
    function XDO_PersistenceManager($connectionFactory) {
        $this->__construct($connectionFactory);
    }

    /**
     * ConnectionFactory を使用して, インスタンスを生成するコンストラクタ.
     *
     * @access public
     * @param ConnectionFactory $connectionFactory ConnectionFactory インスタンス
     */
    function __construct($connectionFactory) {
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * ConnectionFactory を取得する.
     *
     * @access public
     * @return ConnectionFactory ConnectionFactory のインスタンス
     */
    function getConnectionFactory() {
        return $this->connectionFactory;
    }

    /**
     * エンティティクラスの永続化を行う.
     *
     * データストアに, 引数であるエンティティクラスの主キーと一致するデータが
     * 存在する場合, データストアのデータを更新する.
     * 存在しない場合は, エンティティのデータをデータストアに保存する.
     *
     * @access public
     * @param XDO_Entity_Abstract &$entity エンティティクラス
     * @return XDO_Entity_Abstract 引数 $entity のリファレンス
     */
    function makePersistent(&$entity) {
        $pk = array();
        $keys = $entity->getPrimaryKeys();
        $keySize = count($keys);
        for ($i = 0; $i < $keySize; $i++) {
            $pk[$keys[$i]] = $entity->getProperty($keys[$i]);
        }
        $exists = $this->getObjectById($entity, $pk);

        if (!empty($exists)) {
            $where = "";
            for ($i = 0; $i < $keySize; $i++) {
                $where = $keys[$i] . " = " . $pk[$keys[$i]];
                if ($i < $keySize - 1) {
                    $where = " AND ";
                }
            }
            $result = $this->connectionFactory->AutoExecute($entity->getTableName(), $entity->toArray(), 'UPDATE', $where);
        } else {
            $result = $this->connectionFactory->AutoExecute($entity->getTableName(), $entity->toArray(), 'INSERT');
        }
        return $entity;
    }

    /**
     * 複数のエンティティクラスの永続化を行う.
     *
     * この関数は, 一度に複数のエンティティの永続化を行うことができる.
     * その他, 基本的な振舞いは makePersistent() 関数に準拠する.
     *
     * @access public
     * @param array &$entities エンティティクラスの配列
     * @return array エンティティクラスの配列
     */
    function makePersistentAll(&$entities) {
        foreach ($entities as $entity) {
            $this->makePersistent($entity);
        }
        return $entities;
    }

    /**
     * 永続化したエンティティをデータストアから削除する.
     *
     * この関数は, データストアのデータのみが削除される.
     * 引数に渡したエンティティクラスのリファレンスは null にならないので
     * 注意すること.
     *
     * @access public
     * @param XDO_Entity_Abstract &$entity エンティティクラス
     * @return void
     */
    function deletePersistent(&$entity) {
        $sql = "DELETE FROM $entity->tableName WHERE ";
        $size = count($entity->getPrimaryKeys());
        for ($i = 0; $i < $size; $i++) {
            $sql .= $entity->getPrimaryKey($i);
            $sql .= " = ?";
            if ($i < $size - 1) {
                $sql .= " AND ";
            }
        }

        $pk = array();
        $keys = $entity->getPrimaryKeys();
        $keySize = count($keys);
        for ($i = 0; $i < $keySize; $i++) {
            $pk[$keys[$i]] = $entity->getProperty($keys[$i]);
        }

        $result = $this->executeBySql($sql, $pk);
    }

    /**
     * 永続化した複数のエンティティを削除する.
     *
     * この関数は, 一度に複数のエンティティを削除することができる.
     * その他, 基本的な振舞いは deletePersistent() 関数に準拠する.
     *
     * @access public
     * @param array &$entities エンティティクラスの配列
     * @return void
     */
    function deletePersistentAll(&$entities) {
        foreach ($entities as $entity) {
            $this->deletePersistent($entity);
        }
    }

    /**
     * 永続化したエンティティを取得する.
     *
     * この関数で返されるエンティティのインスタンスは, 引数 $entity の
     * 新たなインスタンスである.
     * データストアに, 引数 $id と一致するプライマリーキーのデータが存在しない
     * 場合は, null を返す. この場合, 引数 $entity のリファレンスは
     * null とならない.
     *
     * @access public
     * @param XDO_Entity_Abstract &$entity エンティティクラス
     * @param mixed $id エンティティのプライマリーキー;
     *                  複合キーの場合は, プライマリーキーの配列
     * @return XDO_Entity_Abstract エンティティクラス
     */
    function getObjectById(&$entity, $id) {
        if (!is_a($entity, "XDO_Entity_Abstract")) {
            return null;
        }

        $columns = $entity->getColumnNames();
        $sql = "SELECT ";
        $size = count($columns);
        for ($i = 0; $i < $size; $i++) {
            $sql .= $columns[$i];
            if ($i < $size - 1) {
                $sql .= ", ";
            }
        }
        $sql .= " FROM $entity->tableName WHERE ";

        // TODO WHERE のパターンはキャッシュしたい感じ
        $size = count($entity->getPrimaryKeys());
        for ($i = 0; $i < $size; $i++) {
            $sql .= $entity->getPrimaryKey($i);
            $sql .= " = ?";
            if ($i < $size - 1) {
                $sql .= " AND ";
            }
        }

        $result = $this->connectionFactory->GetAll($sql,
                          is_array($id) ? $id : array($id));
        if (empty($result)) {
            return null;
        } elseif (PEAR::isError($result)) {
            return null; // FIXME
        }else {
            $newEntity = new $entity();
            foreach ($result[0] as $key => $val) {
                $newEntity->setProperty($key, $val);
            }
            return $newEntity;
        }
    }

    /**
     * 永続化した複数のエンティティを取得する.
     *
     * この関数は, 引数 $params に検索パラメータを渡して, 複数のエンティティを
     * 取得する.
     *
     * @access public
     * @param XDO_Entity_Abstract &$entity エンティティクラス
     * @param array $params 検索パラメータの配列
     * @return array エンティティの配列
     */
    function getObjectsByParam(&$entity, $params = array()) {
        if (!is_a($entity, "XDO_Entity_Abstract")) {
            return null;
        }

        $columns = $entity->getColumnNames();
        $sql = "SELECT ";
        $size = count($columns);
        for ($i = 0; $i < $size; $i++) {
            $sql .= $columns[$i];
            if ($i < $size - 1) {
                $sql .= ", ";
            }
        }
        $sql .= " FROM $entity->tableName WHERE ";

        // TODO WHERE のパターンはキャッシュしたい感じ
        $size = count($params);
        $i = 0;
        foreach ($params as $key => $val) {
            $sql .= $key;
            $sql .= " = ?";
            if ($i < $size - 1) {
                $sql .= " AND ";
            }
            $i++;
        }

        return $this->getObjectsBySql($entity, $sql, $params);
    }

    /**
     * SQLクエリを使用して, 永続化されたエンティティを取得する.
     *
     * この関数は, 任意の SQL を使用して, エンティティを取得可能である.
     * パラメータは, ブレースホルダを使用し, 引数 $params に配列で渡すこと.
     * 取得可能なカラムは, エンティティに存在するプロパティに限定されることに
     * 注意する.
     *
     * @access public
     * @param XDO_Entity_Abstract &$entity エンティティクラス
     * @param string $sql 検索を行うための任意の SQL
     * @param array $params 検索パラメータの配列
     * @return array エンティティの配列
     */
    function getObjectsBySql(&$entity, $sql, $params = array()) {
        $results = $this->connectionFactory->GetAll($sql, $params);

        $entities = array();
        if (empty($results)) {
            return null;
        } else {
            foreach ($results as $result) {
                $tmp = new $entity();
                foreach ($result as $key => $val) {
                    $tmp->setProperty($key, $val);
                }
                $entities[] = $tmp;
            }
            return $entities;
        }
    }

    /**
     * 任意の更新系SQLを実行する.
     *
     * この関数は, 任意の更新系 SQL を実行可能である.
     * パラメータは, ブレースホルダを使用し, 引数 $params に配列で渡すこと.
     * 更新を行った行数を返す.
     *
     * @param string $sql 任意の更新系SQL
     * @param array $params パラメータの配列
     * @return integer 更新を行った行数
     */
    function executeBySql($sql, $params = array()) {
        return $this->connectionFactory->executeBySql($sql, $params);
    }

    /**
     * 現在のトランザクションクラスを取得する.
     *
     * トランザクションが開始されていない場合は, トランザクションを開始し,
     * 現在のトランザクションインスタンスを取得する.
     *
     * @access public
     * @return XDO_Transaction 現在のトランザクションインスタンス
     */
    function currentTransaction() {
        if ($this->transaction == null) {
            $this->transaction = new XDO_Transaction($this);
        }
        return $this->transaction;
    }

    /**
     * 引数で指定した名前のシーケンスクラスを取得する.
     *
     * データストアに存在する, 実際のシーケンス名は「$sequenceName + _seq」
     * である.
     * MySQL など, シーケンスをサポートしない RDBMS の場合は, シーケンステーブル
     * をエミュレートし, シーケンスを返す.
     * 
     * @access public
     * @param string $sequenceName シーケンス名
     * @return XDO_Sequence 指定した名前のシーケンスクラス
     */
    function getSequence($seqenceName) {
        return new XDO_Sequence($seqenceName, $this->connectionFactory);
    }

    /**
     * 現在のデータストアへの接続を閉じる.
     *
     * @access public
     * @return void
     */
    function close() {
        $this->connectionFactory->close();
        $this->closed = true;
    }

    /**
     * データストアへの接続が閉じられているかどうか.
     *
     * @access public
     * @return boolean データストアへの接続が閉じられている場合は true
     */
    function isClosed() {
        return $this->closed;
    }
}
?>