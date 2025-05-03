# CodeIgniter 4 Trello Clone API

## 專案說明

這是一個使用 CodeIgniter 4 開發的 Trello Clone API 專案，提供看板、清單、卡片的 CRUD 操作，以及用戶認證和通知功能。

## 資料庫結構

### users 表
```sql
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### boards 表
```sql
CREATE TABLE boards (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  user_id INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### lists 表
```sql
CREATE TABLE lists (
  id INT PRIMARY KEY AUTO_INCREMENT,
  board_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (board_id) REFERENCES boards(id)
);
```

### cards 表
```sql
CREATE TABLE cards (
  id INT PRIMARY KEY AUTO_INCREMENT,
  list_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  position INT NOT NULL DEFAULT 0,
  deadline DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (list_id) REFERENCES lists(id)
);
```

## API 端點說明

### 用戶認證
- `POST /auth/login` - 用戶登入
- `POST /auth/register` - 用戶註冊

### 看板管理
- `GET /boards` - 取得所有看板
- `POST /boards` - 建立新看板
- `GET /boards/{id}` - 取得特定看板
- `PUT /boards/{id}` - 更新看板
- `DELETE /boards/{id}` - 刪除看板

### 清單管理
- `GET /boards/{boardId}/lists` - 取得看板的所有清單
- `POST /boards/{boardId}/lists` - 在看板中建立新清單
- `PUT /lists/{id}` - 更新清單
- `DELETE /lists/{id}` - 刪除清單

### 卡片管理
- `GET /lists/{listId}/cards` - 取得清單的所有卡片
- `POST /lists/{listId}/cards` - 在清單中建立新卡片
- `PUT /cards/{id}` - 更新卡片
- `DELETE /cards/{id}` - 刪除卡片

### 通知功能
- `GET /api/notifications/upcoming` - 取得即將到期的任務

## 安裝與設定

1. 安裝相依套件

```bash
composer install
```

2. 設定環境檔案

將 `env` 檔案複製為 `.env`，並依需求調整（如 baseURL、資料庫設定）：

```bash
cp env .env
```

3. 建立資料庫（如有提供 `database.sql` 檔案，請匯入）

```bash
mysql -u <使用者名稱> -p <資料庫名稱> < database.sql
```

4. 啟動開發伺服器

```bash
php spark serve
```

預設伺服器會於 http://localhost:8080 啟動。

5. 設定 Web Server（正式環境）

請將 Web Server 的根目錄指向 `public` 資料夾。

---

> 本機 MySQL 僅供測試練習用，root 密碼為空字串。
> 連線指令如下：
>
> ```bash
> mysql -u root
> ```

如需詳細設定與使用說明，請參閱 [CodeIgniter 官方文件](https://codeigniter.com/user_guide/)。

## 啟動與關閉 CodeIgniter 伺服器

- 啟動：
  ```bash
  php spark serve
  ```
  執行後終端機會停在該畫面，顯示伺服器已啟動（如 http://localhost:8080）。

- 正確關閉：
  在同一終端機視窗直接按下 `Ctrl + C`，即可完全結束伺服器並釋放 port。

- 注意事項：
  - 請勿使用 `Ctrl + Z`（僅暫停進程，port 仍被佔用）。
  - 若誤用 Ctrl+Z，可用 `fg` 回到前景再 Ctrl+C 結束。
  - 若有殘留進程，可用下列指令查詢並強制結束：
    ```bash
    lsof -i :8080
    kill -9 <PID>
    ```

- 重新啟動：
  先 Ctrl+C 關閉，再重新執行 `php spark serve`。

## 重要設定說明

- `app.baseURL` 請務必設為：
  ```
  app.baseURL = 'http://localhost:8080/'
  ```
- **切勿**將 `baseURL` 設為 `http://localhost:8080/api/`，否則會導致路由、靜態資源、API 路徑錯亂，前端將無法正確連線。
- 前端請直接呼叫 `http://localhost:8080/api/...` 取得資料，無需更動 baseURL。

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
