<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Laravel掲示板アプリ（バックエンド）

Laravel 12を使用した掲示板アプリケーションのバックエンドAPIです。JWT認証を使ったRESTful APIを提供し、掲示板機能（スレッド作成、投稿、返信）をサポートしています。

## 機能

- **認証システム**：JWTを使ったAPIトークン認証
- **掲示板機能**：スレッド作成、投稿、ツリー構造の返信
- **API仕様書**：L5 Swaggerによる自動生成されたAPI仕様書

## 使用技術

- Laravel 12.x
- MySQL 8.0
- JWT Authentication (tymon/jwt-auth)
- L5 Swagger for API Documentation (darkaonline/l5-swagger)
- Docker & Docker Compose

## セットアップ手順

### 1. リポジトリのクローン

```bash
git clone <リポジトリURL>
cd laravel-bbs/backend
```

### 2. Dockerによる環境構築

```bash
docker-compose up -d
```

### 3. 依存パッケージのインストール

```bash
docker exec -it laravel_app composer install
```

### 4. 環境設定

```bash
docker exec -it laravel_app cp .env.example .env
docker exec -it laravel_app php artisan key:generate
docker exec -it laravel_app php artisan jwt:secret
```

### 5. データベースのセットアップ

```bash
docker exec -it laravel_app php artisan migrate
docker exec -it laravel_app php artisan db:seed
```

## APIエンドポイント

### 認証API

- `POST /api/auth/register` - 新規ユーザー登録
- `POST /api/auth/login` - ログイン
- `POST /api/auth/logout` - ログアウト（要認証）
- `POST /api/auth/refresh` - トークン更新（要認証）
- `GET /api/auth/me` - 認証ユーザー情報取得（要認証）

### スレッドAPI

- `GET /api/threads` - スレッド一覧取得
- `GET /api/threads/{id}` - スレッド詳細取得
- `POST /api/threads` - スレッド作成（要認証）
- `PUT /api/threads/{id}` - スレッド更新（要認証、作成者のみ）
- `DELETE /api/threads/{id}` - スレッド削除（要認証、作成者のみ）

### 投稿API（今後実装）

- `GET /api/threads/{thread_id}/posts` - スレッドの投稿一覧取得
- `GET /api/posts/{id}` - 投稿詳細取得
- `POST /api/threads/{thread_id}/posts` - 新規投稿作成（要認証）
- `POST /api/posts/{post_id}/replies` - 投稿への返信作成（要認証）
- `PUT /api/posts/{id}` - 投稿更新（要認証、作成者のみ）
- `DELETE /api/posts/{id}` - 投稿削除（要認証、作成者のみ）

## API仕様書

Swagger UIによるAPI仕様書は以下のURLで確認できます：

```
http://localhost:8080/api/documentation
```

### Swagger更新方法

SwaggerドキュメントはL5 Swaggerを使用して自動生成されています。以下の手順で更新できます：

1. コントローラーにSwaggerアノテーションを追加する
2. 以下のコマンドを実行してドキュメントを生成する

```bash
# Swaggerドキュメントの生成
docker exec -it laravel_app php artisan l5-swagger:generate

# キャッシュをクリアする場合
docker exec -it laravel_app php artisan config:clear
```

基本的な使用例：
```php
/**
 * @OA\Get(
 *     path="/api/threads",
 *     summary="スレッド一覧を取得",
 *     tags={"Threads"},
 *     @OA\Response(
 *         response=200,
 *         description="スレッド一覧を返却",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Thread"))
 *     )
 * )
 */
```

詳しくは[Swagger-PHP公式ドキュメント](https://zircote.github.io/swagger-php/)を参照してください。

## テスト

```bash
docker exec -it laravel_app php artisan test
```

## ライセンス

MIT
