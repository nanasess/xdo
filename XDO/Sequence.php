<?php

/**
 * シーケンスを扱うためのクラス.
 *
 * @package XDO
 * @author Kentaro Ohkouchi
 * @version $Id$
 */
class XDO_Sequence {

    /** シーケンス名. */
    var $sequenceName;
    /** ConnectionFactory インスタンス */
    var $connectionFactory;

    /**
     * シーケンス名と, ConnectionFactory を使用して, インスタンスを生成する
     * コンストラクタ.
     *
     * @param string $sequenceName シーケンス名
     * @param ConnectionFactory $connectionFactory ConnectionFactory インスタンス
     */
    function XDO_Sequence($sequenceName, &$connectionFactory) {
        $this->__construct($sequenceName, $connectionFactory);
    }

    /**
     * シーケンス名と, ConnectionFactory を使用して, インスタンスを生成する
     * コンストラクタ.
     *
     * @param string $sequenceName シーケンス名
     * @param ConnectionFactory $connectionFactory ConnectionFactory インスタンス
     */
    function __construct($sequenceName, &$connectionFactory) {
        $this->sequenceName = $sequenceName;
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * 現在のシーケンス値を取得する.
     *
     * @access public
     * @return long 現在のシーケンス値
     */
    function currentValue() {

    }

    /**
     * 次のシーケンス値を取得する.
     *
     * @access public
     * @return long 次のシーケンス値
     */
    function nextValue() {
        return $this->connectionFactory->nextSequence($this->sequenceName);
    }

    /**
     * シーケンス値を設定する.
     *
     * @access public
     * @return void
     */
    function setValue($value) {
        $this->connectionFactory->setSequence($this->sequenceName, $value);
    }
}
?>
