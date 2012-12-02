twgeojson
=========

行政區域疆界
county = 縣市, town = 鄉鎮市區, village = 村里

村里界圖
--------
資料來源: [國土資訊圖資資訊中心 - 全國村里界圖（台澎金馬）-經緯度](http://tgos.nat.gov.tw/tgos/Web/Metadata/TGOS_MetaData_View.aspx?MID=36646&SHOW_BACK_BUTTON=false)

資料時間: 2012/11/13(已經升格五都)

檔案說明:
- twvillage.json.gz 輸出結果檔，一共 8052 村里，檔案大小 gzip 後還有 24MB 左右
- twvillage.sample.json 跟 twvillage.json 格式一樣，只列出 10 個村里讓大家看看格式。

鄉鎮市界圖/縣市界圖
-------------------
資料來源: [國土資訊系統社會經濟資料庫共通平台](http://segis.moi.gov.tw/STAT/Web/Platform/Product/STAT_ProductFreeList.aspx)

資料時間: 101年(已經升格五都), 98年(五都升格前)

| 檔名                | 說明                         | 大小 |
|---------------------|------------------------------|------|
| twcounty2010.json   | 五都升格後縣市界圖(原始精度), 共22縣市 | 9.9M |
| twcounty2010.2.json | 精度 0.01 資料               | 89K  |
| twcounty2010.3.json | 精度 0.001 資料              | 362K |
| twcounty2010.4.json | 精度 0.0001 資料             | 1.8M |
| twcounty2010.5.json | 精度 0.00001 資料            | 5.9M |
| twcounty2010.6.json | 精度 0.000001 資料           | 8.8M |

| 檔名                | 說明                         | 大小 |
|---------------------|------------------------------|------|
| twtown2010.json     | 五都升格後鄉鎮縣市界圖(原始精度), 共369鄉鎮 | 33M |
| twtown2010.2.json   | 精度 0.01 資料               | 264K  |
| twtown2010.3.json   | 精度 0.001 資料              | 1.1M |
| twtown2010.4.json   | 精度 0.0001 資料             | 4.7M |
| twtown2010.5.json   | 精度 0.00001 資料            | 16M |
| twtown2010.6.json   | 精度 0.000001 資料           | 28M |

- twtown2009.json.gz 輸出結果檔，一共 367 鄉鎮，檔案大小 gzip 後有 12MB 左右
- twcounty2009.json.gz 輸出結果檔，一共 25 縣市，檔案大小 gzip 後還有 3MB 左右

其他
----
- parse.php 將 shp 處理成 geojson 的程式，需要有 shp2pgsql (要裝 postgis)
  只需要將 shp 檔抓下來，再用 parse.php xxx.shp 就會產生 output.json 和 output.sample.json 了
- 如果想要五都升格前的資料，可以到 [https://github.com/g0v/twgeojson](https://github.com/g0v/twgeojson)

