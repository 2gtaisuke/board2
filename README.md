# README

基本設定とUserクラスの基本のみ構築。随時追加する。

## 変更点

* sqlクエリをログへ出力

### ビューコンポーザ

* `app/Http`以下にディレクトリ追加
* アプリケーション名をconfigより定義
* ログイン済みユーザーを$login_userに定義

### ビュー

* `layouts/app`をレイアウトとして定義
* `layouts/_header`を定義

### auth

* `artisan make:auth`
* `redirectTo`を`/`へ書き換え
* `Models\Policies`追加
* `UserPolicy`を追加

### database

* .envにDB情報を記載(現状のdocker用)

### test

* phpunit用に`.env.testing`を作成
* Userクラスのユニットテスト追加
* 認可テスト追加
* duskテスト追加

Userクラスとそれに関わる認可のテストのみ追加している。

### css,js

* fotawesome追加
* vueをとりあえずで消した

### ページネーション

* simple paginationのhtmlを変更

### メッセージ

* `resources/message/ja`追加

### `config/app.php`

* `locale`
* `timezone`
* `faker_locale`
* `name`

### ディレクトリ構成

* App\Models作成