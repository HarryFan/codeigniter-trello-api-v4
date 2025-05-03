# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## 如何啟動本專案

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
