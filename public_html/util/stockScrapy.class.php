<?php
require_once __DIR__ . '/stockCurl.class.php';
require_once __DIR__ . '/tools.php';

/**
 * 用來向複數位置請求資源
 */
class StockScrapy
{
    /**
     * 用來存StockCurl實例
     */
    protected $curlObj;

    /**
     * 用來存要抓的url列表
     * 格式:
     * [
     *     ['url' => '', 'param' => [], 'isPost' => ''],
     * ]
     */
    protected $urlList = [];

    /**
     * 用來存預設的curl選項
     */
    protected $defaultCurlOptions = [];

    /**
     * 建立時注入StockCurl
     * 
     * @param StockCurl $stockCurl
     * @return void
     */
    public function __construct($stockCurl)
    {
        $this->setCurlObj($stockCurl);
    }

    /**
     * 設定底下的
     * 
     * @param StockCurl $obj
     */
    protected function setCurlObj($obj)
    {
        if ( get_class($obj) === 'StockCurl' ) {
            $this->curlObj = $obj;
        } else {
            throw new Exception('Injection Object is not corrected');
        }      
    }
    
    /**
     * 取得urlList
     * 
     * @return array
     */
    public function getUrlList()
    {
        return $this->urlList;
    }

    /**
     * 設定要爬的url列表
     * 
     * @param array $urlList
     * @return boolean
     */
    public function setUrlList($urlList)
    {
        if ( !tools_isArrayExceptNull($urlList) ) {
            return false;
        }

        if ( $this->validateUrlList($urlList) ) {
            $this->urlList = $urlList;
            return true;
        }

        return false;
    }

    /**
     * 驗證urlList內單筆資料的格式
     * 
     * @param array $urlData
     * @return boolean
     */
    protected function validateUrlData($urlData)
    {
        if ( !tools_isArrayExceptNull($urlData) ) {
            return false;
        }

        $compareData = [
            'url' => '', 'param' => [], 'isPost' => 0
        ];

        // 判斷key是否存在
        if ( array_diff_key($compareData, $urlData) !== [] ) {
            return false;
        }

        $checkType = is_string($urlData['url']) && 
                     is_array($urlData['param']) && 
                     tools_isOneOrZero($urlData['isPost']);
        
        if ( $checkType ) {
            return true;
        }

        return false;
    }

    /**
     * 驗證urlList內的資料
     * 
     * @param array $urlList
     * @return boolean
     */
    protected function validateUrlList($urlList)
    {
        if ( !tools_isArrayExceptNull($urlList) ) {
            return false;
        }

        foreach ( $urlList as $urlData ) {
            if ( !$this->validateUrlData($urlData) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * 取得預設的curl選項
     * 
     * @return array
     */
    public function getCurlOptions()
    {
        return $this->defaultCurlOptions;
    }

    /**
     * 設定預設的curl選項
     * 
     * @param array $options
     * @return boolean
     */
    public function setCurlOptions($options)
    {
        if ( tools_isArrayExceptNull($options) ) {
            $this->defaultCurlOptions = $options;
            return true;
        }

        return false;
    }

    /**
     * 用來處理此次request所需要的curl選項
     * 
     * @param array $param
     * @return array
     */
    protected function processCurlOptions($param)
    {
        $options = $this->defaultCurlOptions;

        // 假設有設定預設CURLOPT_POST，有此key值必須再重設
        if ( $param['isPost'] ) {
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
        } else {
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        }

        if ( !tools_isArrayExceptNull($param['param']) ) {
            return $options;
        }

        $options[CURLOPT_POSTFIELDS] = http_build_query($param['param']);

        return $options;
    }

    /**
     * 根據urlList來爬
     */
    public function runScrapy()
    {
        if ( !$this->validateUrlList($this->urlList) ) {
            return false;
        }

        $res = [];

        foreach ( $this->urlList as $urlData ) {
            $this->curlObj->setUrl($urlData['url']);

            $options = $this->processCurlOptions($urlData);
            $this->curlObj->setCurlOptions($options);

            $data = $this->curlObj->runRequest();
            $res[$urlData['url']] = $data;
        }

        return $res;
    }

    /**
     * error_log封裝
     * 
     * @param  string $error
     * @return void
     */
    private function errorLog($error)
    {
        $message = ' [ stockScrapy ] ' . $error;

        error_log($message);
    }
}