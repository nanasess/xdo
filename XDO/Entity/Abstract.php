<?php

/**
 * すべてのエンティティの抽象クラス.
 *
 * @package XDO_Entity
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_Entity_Abstract {

    /** テーブル名. */
    var $tableName;
    /** カラム名の配列. */
    var $columnNames = array();
    /** プライマリーキーの配列. */
    var $primaryKeys = array();
    /** 読み取り専用かどうか. */
    var $readOnly = false;
    /** 検索用SQLクエリ. */
    var $select;

    /**
     * このエンティティのプライマリーキー名を配列で返す.
     *
     * @return array プライマリーキー名の配列
     */
    function getPrimaryKeys() {
        return $this->primaryKeys;
    }

    /**
     * このエンティティのプライマリーキー名をインデックス値で検索して返す.
     *
     * @param integer $index インデックス値
     * @return string インデックス値と一致するプライマリーキー名
     */
    function getPrimaryKey($index) {
        return $this->primaryKeys[$index];
    }

    /**
     * このエンティティのプライマリーキーと値を連想配列で返す.
     *
     * "プライマリーキー名" => "プライマリーキーの値" の形式の連想配列を生成して
     * 返す.
     *
     * @return array プライマリーキーと値の連想配列
     */
    function getPrimaryKeyAndValues() {
    }

    /**
     * プロパティ名を引数に, プロパティの値を返す.
     *
     * @param string $propertyName プロパティ名
     * @return mixed プロパティの値
     */
    function getProperty($propertyName) {
        return $this->$propertyName;
    }

    /**
     * 引数で指定したプロパティに値を設定する.
     *
     * この関数は, 自分自身のインスタンスを返すため, Chain Of Responsibility
     * パターンが使用可能である.
     *
     * @param string $propertyName プロパティ名
     * @param mixed プロパティに設定する値
     * @return XDO_Entity_Abstract 自分自身のインスタンス
     */
    function setProperty($propertyName, $value) {
        $this->$propertyName = $value;
        return $this;
    }

    /**
     * カラム名を配列で取得する.
     *
     * @return array カラム名の配列
     */
    function getColumnNames() {
        return $this->columnNames;
    }

    /**
     * テーブル名を取得する.
     *
     * @return string テーブル名
     */
    function getTableName() {
        return $this->tableName;
    }

    /**
     * 自分自身のクラス名を取得する.
     *
     * この関数は get_class() 関数へのラッパーです.
     *
     * @return string 自分自身のクラス名
     * @see get_class
     */
    function getClassName() {
        return get_class($this);
    }

    /**
     * このクラスで使用する検索用SQLを返す.
     *
     * このクラスの永続化データを PersistenceManager で検索する際に使用する
     * SQLを返す.
     *
     * @return string このクラスで使用する検索用 SQL
     */
    function getSelect() {
        return $this->select;
    }

    /**
     * このクラスで使用する任意の検索用SQLを設定する.
     *
     * このクラスの永続化データを PersistenceManager で検索する際に使用する
     * SQL を設定する.
     *
     * @param string $select 検索用 SQL の文字列
     * @return XDO_Entity_Abstract 自分自身のインスタンス
     */
    function setSelect($select) {
        $this->select = $select;
        return $this;
    }

    /**
     * この永続化したエンティティが読み取り専用かどうか.
     *
     * 結合した複数のテーブルであったり, VIEW のように, 読み取り専用の場合は true
     *
     * @return boolean エンティティが読み取り専用の場合 true
     */
    function isReadOnly() {
        return $this->readOnly;
    }

    /**
     * この永続化したエンティティを読み取り専用かどうか設定する.
     *
     * @param boolean $boolean エンティティを読み取り専用にする場合 true
     */
    function setReadOnly($boolean) {
        $this->readOnly = $boolean;
        return $this;
    }

    /**
     * このエンティティの文字列表現を出力する.
     *
     * @return string このエンティティの文字列表現
     */
    function toString() {
        $result = "";
        foreach ($this->columnNames as $column) {
            $result .= "[" . $column . "]=>" . $this->$column . ",";
        }
        return $result;
    }

    /**
     * このエンティティのプロパティとプロパティの値を連想配列で返す.
     *
     * "プロパティ名" => "プロパティの値" の形式の連想配列を生成して返す.
     *
     * @return array このエンティティのプロパティとプロパティの値の連想配列
     */
    function toArray() {
        $result = array();
        foreach ($this->columnNames as $column) {
            $result[$column] = $this->$column;
        }
        return $result;
    }
}
?>