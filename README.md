twgeojson
=========

行政區域疆界

村里界圖
--------
資料來源: [國土資訊圖資資訊中心 - 全國村里界圖（台澎金馬）-經緯度](http://tgos.nat.gov.tw/tgos/Web/Metadata/TGOS_MetaData_View.aspx?MID=36646&SHOW_BACK_BUTTON=false)

資料時間: 2012/11/13(已經升格五都)

檔案說明:
- twvillage.json.gz 輸出結果檔，一共 8052 村里，檔案大小 gzip 後還有 24MB 左右
- twvillage.sample.json 跟 twvillage.json 格式一樣，只列出 10 個村里讓大家看看格式。

鄉鎮市界圖
----------
資料來源: [國土資訊系統社會經濟資料庫共通平台 - 100年全國鄉鎮市區界圖](http://segis.moi.gov.tw/STAT/Web/Platform/Product/STAT_ProductFreeList.aspx)

資料時間: 101年(已經升格五都)

檔案說明:
- twtown.json.gz 輸出結果檔，一共 369 鄉鎮，檔案大小 gzip 後還有 11MB 左右
- twtown.sample.json 跟 twtown.json 格式一樣，只列出一個鄉鎮讓大家看看格式
- twcounty.json.gz 輸出結果檔，一共 22 鄉鎮，檔案大小 gzip 後還有 3MB 左右

其他
----
- parse.php 將 shp 處理成 geojson 的程式，需要有 shp2pgsql (要裝 postgis)
  只需要將 shp 檔抓下來，再用 parse.php xxx.shp 就會產生 output.json 和 output.sample.json 了
- 如果想要五都升格前的資料，可以到 [https://github.com/g0v/twgeojson](https://github.com/g0v/twgeojson)

