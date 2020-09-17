<?php
require_once __DIR__ . '/tools.php';

/**
 * 用來封裝原生curl
 * 
 */
class StockCurl
{
    /**
     * 用來存要請求資源的位置(url)
     */
    protected $url;

    /**
     * 用來存預設的curl選項
     * (可改存到DB)
     */
    const CURL_OPTIONS = [
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.135 Safari/537.36',
        CURLOPT_RETURNTRANSFER => true
    ];

    /**
     * 用來存curl所設定的選項
     */
    protected $curlOptions;

    /**
     * 若無傳參就給''
     * 
     * @param string $url
     * @return void
     */
    public function __construct($url='')
    {
        if ( tools_isNull($url) ) {
            $this->setUrl('');
        } else {
            $this->setUrl($url);
        }

        $this->initializeCurlOptions();
    }

    /**
     * 設定請求資源的位置
     * 
     * @param string $url
     * @return void
     */
    public function setUrl($url='')
    {
        $this->url = $url;
    }

    /**
     * 查看目前所設置的url
     * @param void
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 驗證url是否存在
     * 
     * @return boolean
     */
    protected function validateUrl()
    {
        if ( tools_isStringExceptNull($this->url) ) {
            return false;
        }

        return true;
    }

    /**
     * curl選項初始化
     * 
     * @return voic
     */
    public function initializeCurlOptions()
    {
        $this->curlOptions = self::CURL_OPTIONS;
    }

    /**
     * 用來設定curl所需設定的選項
     * 
     * @param array $options
     * @return boolean
     */
    public function setCurlOptions($options)
    {
        if ( tools_isArrayExceptNull($options) ) {
            $this->curlOptions = $options;
            return true;
        }

        return false;
    }

    /**
     * 如果有錯誤發生，所執行的動作
     * 錯誤代碼參考
     * https://curl.haxx.se/libcurl/c/libcurl-errors.html
     * 
     * @param object $curlObj
     * @return string
     */
    protected function processError($curlObj)
    {
        $errorNo = curl_errno($curlObj);
        $message = ' error no: ' . $errorNo . 
                   ' message: ' . curl_strerror($errorNo);

        $this->errorLog($message);
        return $message;
    }

    /**
     * 用來處理最後所取得的資料，轉為特定的array格式回傳
     * 
     * @param any $value
     * @param string $info
     * @return array
     */
    protected function processResult($value, $info='')
    {
        $data = [
            'status' => 'fail',
            'info' => $info,
            'data' => $value
        ];

        if ( $value === false ) {
            $data['data'] = null;
        } else {
            $data['status'] = 'success';
        }

        return $data;
    }

    /**
     * 取得目前的選項
     * 
     * @return array
     */
    public function getCurlOptions()
    {
        return $this->curlOptions;
    }

    /**
     * 執行整個request流程
     * 
     * @return array
     */
    public function runRequest()
    {
        if ( $this->validateUrl() ) {
            return $this->processResult($res, 'url is not corrected');
        }

        // request 流程開始
        $ch = curl_init($this->url);

        $opt = $this->getCurlOptions();
        curl_setopt_array($ch, $opt);

        $res = curl_exec($ch);

        $info = 'success';
        if ( $res === false ) {
            $info = $this->processError($ch);
        }

        // request 流程結束
        $this->closeCurl($ch);

        return $this->processResult($res, $info);
    }

    /**
     * 初始化選性並結束curl
     * 
     * @param object $curlObj
     * @return void
     */
    protected function closeCurl($curlObj)
    {
        $this->initializeCurlOptions();
        curl_close($curlObj);
    }

    /**
     * error_log封裝
     * 
     * @param  string $error
     * @return void
     */
    private function errorLog($error)
    {
        $message = ' [ stockCurl ] ' . $error;

        error_log($message);
    }
}