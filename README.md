# CodeIgniter 4 Trello Clone API

## 專案概述

這是一個使用 CodeIgniter 4 開發的 Trello Clone API 專案，提供完整的看板、清單、卡片的 CRUD 操作，以及用戶認證、通知功能和截止日期提醒系統。該 API 設計支援多種前端介面（如 Vue、React）的無縫整合，提供穩定可靠的後端服務。

## 已完成功能

### 1. 核心功能

- **用戶管理**
  - 用戶註冊：支援新用戶建立帳號
  - 用戶登入：使用電子信箱和密碼進行身份驗證
  - JWT 身份驗證：所有 API 路由均受到 JWT 保護
  - 身份驗證中間件：自動驗證 API 請求的合法性

- **看板管理**
  - 建立看板：支援用戶建立多個專案看板
  - 查詢看板：獲取用戶所有看板列表
  - 更新看板：修改看板標題和其他屬性
  - 刪除看板：移除不需要的看板

- **清單管理**
  - 建立清單：在特定看板中創建任務清單
  - 查詢清單：獲取看板內所有清單
  - 重新排序：支援清單順序調整
  - 刪除清單：移除特定清單及其包含的所有卡片

- **卡片管理**
  - 完整 CRUD：支援卡片的創建、讀取、更新、刪除
  - 卡片排序：在清單內及跨清單的卡片拖曳排序
  - 截止日期：為卡片設定精確到分鐘的截止時間
  - 卡片詳情：支援豐富的卡片內容（標題、描述、截止日期等）

- **通知系統**
  - 即將到期任務提醒：自動檢測即將到期的任務
  - 事件觸發通知：任務狀態變更時發送通知
  - 通知標記：支援已讀/未讀狀態管理

### 2. 性能與安全特性

- **高效資料庫操作**
  - 優化的查詢：避免 N+1 查詢問題
  - 事務支援：確保資料一致性
  - 索引優化：提升查詢效能

- **安全機制**
  - 輸入驗證：所有用戶輸入經過嚴格驗證
  - XSS 防護：自動轉義危險字符
  - SQL 注入防護：使用參數化查詢
  - CORS 支援：適當的跨域資源分享設定

- **擴展性設計**
  - 模組化結構：便於功能擴展
  - RESTful API：符合 REST 設計原則
  - 詳細錯誤處理：前端友好的錯誤訊息

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

## API 端點詳細說明

### 用戶認證
- `POST /auth/login` - 用戶登入
  - 請求參數：email, password
  - 回傳：JWT token 及用戶資訊
  
- `POST /auth/register` - 用戶註冊
  - 請求參數：name, email, password
  - 回傳：新建用戶資訊

### 看板管理
- `GET /boards` - 取得所有看板
  - 查詢參數：user_id (可選)
  - 回傳：看板列表

- `POST /boards` - 建立新看板
  - 請求參數：title, user_id
  - 回傳：新建看板資訊

- `GET /boards/{id}` - 取得特定看板
  - 路徑參數：id
  - 回傳：看板詳細資訊

- `PUT /boards/{id}` - 更新看板
  - 路徑參數：id
  - 請求參數：title
  - 回傳：更新後看板資訊

- `DELETE /boards/{id}` - 刪除看板
  - 路徑參數：id
  - 回傳：刪除成功訊息

### 清單管理
- `GET /boards/{boardId}/lists` - 取得看板的所有清單
  - 路徑參數：boardId
  - 回傳：清單列表及內含卡片

- `POST /boards/{boardId}/lists` - 在看板中建立新清單
  - 路徑參數：boardId
  - 請求參數：title, position
  - 回傳：新建清單資訊

- `PUT /lists/{id}` - 更新清單
  - 路徑參數：id
  - 請求參數：title, position
  - 回傳：更新後清單資訊

- `DELETE /lists/{id}` - 刪除清單
  - 路徑參數：id
  - 回傳：刪除成功訊息

### 卡片管理
- `GET /lists/{listId}/cards` - 取得清單的所有卡片
  - 路徑參數：listId
  - 回傳：卡片列表

- `POST /lists/{listId}/cards` - 在清單中建立新卡片
  - 路徑參數：listId
  - 請求參數：title, description, position, deadline
  - 回傳：新建卡片資訊

- `PUT /cards/{id}` - 更新卡片
  - 路徑參數：id
  - 請求參數：title, description, list_id, position, deadline
  - 回傳：更新後卡片資訊

- `DELETE /cards/{id}` - 刪除卡片
  - 路徑參數：id
  - 回傳：刪除成功訊息

### 通知功能
- `GET /api/notifications/upcoming` - 取得即將到期的任務
  - 查詢參數：minutes (可選) - 指定檢查未來多少分鐘內到期的任務，預設值為 30
  - 回傳：即將到期卡片列表包含標題、截止時間、所屬清單等資訊

- `POST /api/notifications/settings` - 更新通知偏好設定
  - 請求參數：browser_enabled, email_enabled, polling_interval, email_lead_time
  - 回傳：更新成功訊息

- `POST /api/notifications/test-email` - 測試電子郵件發送功能
  - 請求參數：email - 收件者電子郵件地址
  - 回傳：發送結果狀態
  
### 排程任務
- `php spark notifications:send` - 發送任務到期電子郵件通知
  - 功能：根據用戶設定的提前提醒時間，發送即將到期任務的電子郵件提醒
  - 建議：設置為每小時執行一次的排程任務

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

預設伺服器會於 http://localhost:8890 啟動。

5. 設定 Web Server（正式環境）

請將 Web Server 的根目錄指向 `public` 資料夾。

## 技術棧

- CodeIgniter 4 框架
- PHP 8.1+
- MySQL/MariaDB
- JWT 身份驗證
- RESTful API 設計
- JSON 資料格式

## 注意事項

- API 路徑應始終以 `/api/` 開頭
- 請確保 CORS 設定正確，允許前端域名訪問
- 所有 API 回應均採用 JSON 格式
- 錯誤處理採用標準 HTTP 狀態碼

如需詳細設定與使用說明，請參閱 [CodeIgniter 官方文件](https://codeigniter.com/user_guide/)。
