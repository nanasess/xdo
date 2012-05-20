# 概要

Java の JDO(Java Data Objects - JSR243) のような O/R Mapper を PHP で実現する.

## 導入要件
 - TABLE VIEW に対して Model を作成可能なこと
 - 複雑な JOIN に対応可能なこと
   - 複雑な JOIN をしても, 返り値は Model にしたい

## サンプルコード(案)

### DB に INSERT
プライマリーキーが一致するデータが存在する場合は UPDATE になります.

```php
<?php
$client = new Client();
$client->name01 = "名前(姓)";
$client->name02 = "名前(名)";
    
... snip
    
$pm = getPersistenceManager();
$pm->makePersistent($client);
```
### DB からデータを取得する場合.

```php
<?php
$pm = getPersistenceManager();
// PK が 1 の dtb_client のデータを取得する
$client = $pm->getObjectById(new Client(), 1);
```

#### 複数のレコードを取得する場合. limit, offset は, 引数で指定

```php
<?php
$pm = getPersistenceManager();
//  dtb_client の column1 が expression に一致するデータを取得する
$client = $pm->getObjectsById(new Client(), array("column1" => "expression"));
```

$clients には, dtb_client の連想配列が入る.

#### LIKE 検索は, 連想配列のキーに suffix をつける.
suffix のルールによって, いろいろな検索条件の指定ができそう

```php
<?php
$pm = getPersistenceManager();
// email が nanasess を含むものを検索する. %value% のみのサポート
$clients = $pm->getObjectsById(new Client(), array("email__like" => "nanasess"));
```


#### データを削除する場合

```php
<?php
$pm = getPersistenceManager();
// $client のプライマリーキーに一致するデータを削除する
$pm->deletePersistent($client);
```

#### SQL直書きにも対応

```php
<?php
$pm = getPersistenceManager();
$client = $pm->getObjectsBySql(new Client(), "SELECT * FROM dtb_client WHERE client_id = ?", array(1));
```

#### 更新系のクエリは, executeBySql() を使用

```php
<?php
$pm = getPersistenceManager();
$pm->executeBySql("UPDATE dtb_client SET name = ? WHERE client_id = ?", array('hogehoge', 1));
```

#### トランザクションは, Transaction クラスを使用

```php
<?php
$pm = getPersistenceManager();
$tx = $pm->currentTransaction();
$tx->begin();

... snip

$tx->commit();
```

#### シーケンスは, Sequence クラスを使用

```php
<?php
$pm = getPersistenceManager();
$sequence = $pm->getSequence('dtb_client_client_id_seq');
$client->client_id = $sequence->nextValue();
$pm->makePersistent($client);
```


### 実装(案)

 - 各テーブルのメタデータは,  Abstract な Entity クラスで実装する
 - 各DBの差異は, ADOdb などの抽象化ドライバで吸収する
 - SELECT とか, INSERT とかの処理は, PersistenceManager クラスが抽象化して行う.

### 長所

 - DB を意識しなくてよい
 - ソースコードがものすごくシンプルになる
 - 慣れればメンテしやすい

### 短所

 - ~~もしかしたら車輪の再発明？~~ 良いものが無いので自分で作ります
 - 慣れるまで大変
 - ~~SQL をゴリゴリ書けない~~ 個別のクエリ, 複雑に JOIN した SQL や VIEW にも対応予定
 - ~~PHP4 で, どこまで実現できるか不明~~ 今のところ PHP4 でも問題無し
 - PersistenceManager とか, Builder 用のクラスがものすごいことになりそう
 - ~~あんまり速くない(たぶん)~~ ADOdb の薄いラッパーなので, パフォーマンス悪くないはず

### その他検討事項
 - エラーハンドリングの方法を要検討
   - PEAR::Error を返す
   - ADOdb のように false を返して, エラーメッセージを取得する
   - trigger_error を使用する
   - 独自の方法. PersistenceManager::getErrorHandler() とか.
 - limit, offset とか, order by の実装方法
   - エンティティにセットするのが良い?

