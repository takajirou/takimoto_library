# takimoto_library 環境構築

PHPだけでMVC APIを作る前提の、MySQL開発環境です。
ここではAPI本体は作らず、DBを起動するための環境だけを用意しています。

## 起動

```bash
docker compose up -d
```

## 停止

```bash
docker compose down
```

## DB接続情報

PHPから接続する場合は、`.env.example` を参考に `.env` を作成してください。

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=takimoto_library
DB_USERNAME=takimoto_user
DB_PASSWORD=takimoto_password
```

## Adminer

ブラウザで以下を開くと、DBを確認できます。

```text
http://127.0.0.1:8080
```

Adminerの入力内容:

```text
システム: MySQL
サーバ: mysql
ユーザ名: takimoto_user
パスワード: takimoto_password
データベース: takimoto_library
```

## 初期テーブル

初回起動時に、以下のテーブルが作成されます。

- `students`
- `books`
- `loans`
- `admins`

初期SQLは `database/init/001_create_tables.sql` にあります。

## テスト用DB

テストはアプリ用DBとは別の `takimoto_library_test` を使用します。PHPUnit/Pest実行時は、`phpunit.xml` がこのDB名を自動で指定します。

Dockerを新規起動した場合は `database/init/002_create_test_tables.sql` により、本番用と同じテーブルが自動作成されます。既にMySQLのボリュームを作成済みの場合は、次のコマンドでテスト用DBだけを追加できます。

```bash
docker compose exec -T mysql mysql -uroot -ptakimoto < database/init/002_create_test_tables.sql
```

接続情報が必要な場合は、`.env.testing.example` を参考にしてください。

## DBを作り直したい場合

テーブル定義を変えて最初から作り直す場合は、MySQLのボリュームも削除します。

```bash
docker compose down -v
docker compose up -d
```
