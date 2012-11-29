twgeojson
=========

行政區域疆界

資料來源: [國土資訊圖資資訊中心 - 全國村里界圖（台澎金馬）-經緯度](http://tgos.nat.gov.tw/tgos/Web/Metadata/TGOS_MetaData_View.aspx?MID=36646&SHOW_BACK_BUTTON=false)

資料時間: 2012/11/13

這邊只有放里界資訊
如果要縣界和鄉界的話，可以到 [https://github.com/g0v/twgeojson](https://github.com/g0v/twgeojson)

檔案說明:
- output.json.gz 輸出結果檔，一共 8052 村里，檔案大小 gzip 後還有 30MB 左右
- output.sample.json 跟 output.json 格式一樣，只列出 10 個村里讓大家看看格式。
- parse.php 將 shp 處理成 geojson 的程式，需要有 shp2pgsql (要裝 postgis)

只需要將國土資訊圖資資訊中心的原表抓下來，再用 parse.php xxx.shp 就會產生 output.json 和 output.sample.json 了
