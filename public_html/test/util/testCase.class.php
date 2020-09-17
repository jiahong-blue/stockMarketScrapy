<?php
/**
 * 簡單的測試值及型別
 */

 /**
  * 測試用class
  *
  */
class TestCase
{
    /**
     * 用來存測試用class的實俐
     * 
     * @var object
     */
    protected $testObj;

    /**
     * 用來存方法的array
     * 
     * @var Array
     */
    protected $methodList;

    /**
     * 用來存已經測過的功能名子
     * 
     * @var Array
     */
    protected $methodListFinshedTest = [];

    /**
     * @param object $obj 傳入建好的物件
     */
    public function __construct(object $obj)
    {
        $this->testObj = $obj;

        $this->methodList = get_class_methods($obj);
    }

    /**
     * 傳入方法執行完的結果以及所預測會得到的結果，相等回傳true，不相等回傳false
     * 
     * @param any $returnValue
     * @param any $predictValue
     * @return boolean
     */
    static public function testValue($returnValue, $predictValue)
    {
        return $returnValue === $predictValue;
    }

    /**
     * 傳入方法執行完的結果其型別以及所預測會得到的型別，相等回傳true，不相等回傳false
     * 
     * @param any $returnType
     * @param any $predictType
     * @return boolean
     */
    static public function testType($returnType, $predictType)
    {
        return $returnType === $predictType;
    }

    /**
     * 測試非class的method
     * 
     * @param string $methodName
     * @param array $param
     * @param string $comment
     * @return void
     */
    static public function testNonClassMethod($methodName, $param, $comment=null)
    {
        static::echoFormat([
            'message' => str_pad(
                ' start test ' . $methodName . ' ', 50, "=", STR_PAD_BOTH
            )
        ]);

        if ( $comment ) {
            static::echoFormat(['message' => $comment]);
        }

        if ( static::testValue($param['result'], $param['predictValue']) ) {
            static::echoFormat([ 'status' => 'success', 'message' => ' value ok ']);
        } else {
            static::echoFormat([ 'status' => 'error', 'message' => ' value not ok ']);
        }

        if ( static::testValue(gettype($param['result']), $param['predictType']) ) {
            static::echoFormat([ 'status' => 'success', 'message' => ' type ok ']);
        } else {
            static::echoFormat([ 'status' => 'error', 'message' => ' type not ok ']);
        }

        static::echoFormat(
            [
                'message' => str_pad(' end ', 50, '=', STR_PAD_BOTH)
            ]
        );
    }

    /**
     * 驗證class中是否有其method
     * 
     * @param object $obj
     * @param string $methodName
     * @return boolean
     */
    public function isMethodExist($obj, $methodName)
    {
        return method_exists($obj, $methodName);
    }

    /**
     * 驗證陣列是否帶有特定的key
     * 
     * @param array $param
     * @param array $paramKeys
     * @return boolean
     */
    public function validateParamKey($param, $paramKey)
    {
        $keys = array_keys($param);

        return array_diff($paramKey, $keys) === [];
    }

    /**
     * 紀錄已經完成測試的方法
     * 
     * @param string $methodName
     * @return void
     */
    protected function commitFineshedMethod($methodName)
    {
        array_push($this->methodListFinshedTest, $methodName);
    }

    /**
     * 輸出所有方法名子
     * 
     * @return void
     */
    public function printMethodList()
    {
        $this->echoFormat(['message' => implode(',', $this->methodList)]);
    }

    /**
     * 輸出已經測過的方法名子
     * 
     * @return void
     */
    public function printMethodListFinished()
    {
        $this->echoFormat([
            'message' => implode(',', $this->methodListFinshedTest)
        ]);
    }

    /**
     * 輸出還未測過的方法名子
     * 
     * @return void
     */
    public function printMethodListNotFinshed()
    {
        $notFinshed = array_diff(
            $this->methodList, $this->methodListFinshedTest
        );

        $this->echoFormat(['message' => implode(',', $notFinshed)]);
    }

    /**
     * 傳入想要測試的function名稱
     * 
     * @param string $methodName 方法名
     * @param array $param 方法所需參數
     * @param string $comment 說明
     * @return void
     */
    public function testMethod($methodName, $param, $comment=null)
    {
        $this->echoFormat([
            'message' => str_pad(
                ' start test ' . $methodName . ' ', 50, "=", STR_PAD_BOTH
            )
        ]);

        if ( $comment ) {
            $this->echoFormat([
                'message' => $comment
            ]);
        }

        // 方法不存在就停
        if ( !$this->isMethodExist($this->testObj, $methodName) ) {
            $this->echoFormat([
                'status' => 'error', 
                    'message' => $methodName . ' is not exist'
            ]);
            return;
        }
        
        // 驗證參數格式
        $validateParam = ['predictValue', 'predictType', 'param'];
        if ( !$this->validateParamKey($param, $validateParam) ) {
            $this->echoFormat([
                'status' => 'error',
                    'message' => ' keys of $param '. 
                        implode(',', $validateParam) . ' is not exist'
            ]);
            return;
        }

        // 執行方法
        $res = call_user_func_array( array($this->testObj, $methodName), $param['param'] );

        // 測試結果是否和預測一致
        if ( $this->testValue($res, $param['predictValue']) ) {
            $this->echoFormat([ 'status' => 'success', 'message' => ' value ok ']);
        } else {
            $this->echoFormat(
                [ 'status' => 'error', 'message' => ' value is not corrected ']
            );
        }

        // 測試結果的型別是否和預測的一致
        if ( $this->testType(gettype($res), $param['predictType']) ) {
            $this->echoFormat([ 'status' => 'success', 'message' => ' type ok ']);
        } else {
            $this->echoFormat(
                [ 'status' => 'error', 'message' => ' type is not corrected ']
            );
        }

        $this->commitFineshedMethod($methodName);

        $this->echoFormat(
            ['message' => str_pad(' end ', 50, "=", STR_PAD_BOTH)]
        );
    }

    /**
     * echo封裝
     * 
     * @param string $param
     * @return void
     */
    static public function echoFormat($param=null) 
    {
        if ( tools_isNull($param) ) {
            echo '<div style="color: red;">parameter is null</div>';
            return;
        }

        if ( array_key_exists('status', $param) ) {
            $color = '';
            switch ( $param['status'] ) {
                case 'success':
                    $color = 'green';
                    break;
                case 'error':
                    $color = 'red';
                    break;
            }

            echo "<div style='color: $color;'> [ " . $param['status'] . ' ] ' .
                  $param['message'] . '</div>';
            return;

        } else {
            echo '<div>' . $param['message'] . '</div>';
            return;
        }
    }

}