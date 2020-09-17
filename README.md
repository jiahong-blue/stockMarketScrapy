## 概要
原本自己所參考的大盤股市相關資訊以excel所存，但須手動更新的緣故，所以修改為使用webapp的方式來呈現，為了能快速修改，以及環境變數的統一，使用docker來建置環境，分為5個container來處理不同的程式

* apache
使用httpd的鏡像來建置web server

* mysql
資料庫使用廣泛運用的mysql，docker hub有提供已經建置好的環境可以直接使用

* php
後端使用php語言，不使用框架減少體積，用原生的php來處理client的request，基本只回傳json格式的資料

* vue
前端測試時所使用的container，用來跑vue-cli的環境

* scrapy
用來抓取股市大盤的相關資料，使用php來撰寫爬蟲程式，每天固定時間來抓取
https://www.taifex.com.tw/
https://www.twse.com.tw


## 展示
![image](https://github.com/jiahong-blue/stockMarketScrapy/blob/master/src/gif/show.gif)