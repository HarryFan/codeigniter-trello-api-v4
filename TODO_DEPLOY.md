# CodeIgniter Trello API 部署待辦事項

本文件記錄部署到 Bluehost 時尚未完成的步驟，供後續操作參考。

---

## 待辦事項

1. **建立資料庫與用戶**
   - 進入 Bluehost 控制台 → Advanced → MySQL Databases。
   - 建立新資料庫與用戶，並賦予權限。

2. **匯入資料表結構**
   - 進入 phpMyAdmin，選擇資料庫後 Import SQL 檔案（如 database.sql）。

3. **設定資料庫連線**
   - 編輯 `app/Config/Database.php`，填入 Bluehost 資料庫資訊（DB 名稱、用戶、密碼）。

4. **確認權限**
   - 確認 `writable` 目錄有正確寫入權限（755 或 775）。

5. **測試 API 功能**
   - 用 Postman 或前端測試 API 路徑是否正常運作。

---

如遇資料庫連線、匯入、權限等問題，請記錄錯誤訊息以利後續排查。

> 本檔案僅供部署流程追蹤與提醒，完成後可刪除或歸檔。
