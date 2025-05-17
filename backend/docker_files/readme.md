# Laravel BBS Backend

これは Laravel で構築された掲示板アプリケーションのバックエンド API です。

## セットアップ

### 1. Docker 環境の構築

```bash
# backend ディレクトリに移動
cd backend

# Docker コンテナのビルドと起動
docker-compose up -d --build
```

### 2. Laravel プロジェクトのセットアップ

```bash
# Composer パッケージのインストール
docker-compose exec app composer install

# .env ファイルの作成
cp .env.example .env

# アプリケーションキーの生成
docker-compose exec app php artisan key:generate

# JWT Secret の生成
docker-compose exec app php artisan jwt:secret

# データベースマイグレーションとシーディング
docker-compose exec app php artisan migrate --seed
```

## API エンドポイント

(後で追記)

## データベース構成

(後で追記)

## OpenAPI (Swagger) ドキュメント

(後で追記)
