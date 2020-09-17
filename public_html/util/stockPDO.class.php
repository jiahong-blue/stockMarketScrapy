<?php
require_once('config.php');
require_once('tools.php');

/**
 * PDO封裝
 * 
 * @version 1.0
 * @author hong
 */
class StockPDO
{

    /**
     * StockPDO實例
     */
    static private $instance;

    /**
     * PDO實俐
     */
    private $pdo;

    /**
     * 最後一筆ID
     */
    private $lastInsertId;

    /**
     * 取得PDO單利例(singleton)
     */
    static public function getInstance()
    {
        if ( !static::$instance ) {
            static::$instance = new StockPDO;
        }

        return static::$instance;
    }

    /**
     * 建構
     * 
     * @return void
     */
    private function __construct() 
    {
        $this->pdo = $this->initializePDO();
        // 設定時區
        $this->pdo->query('SET time_zone="+08:00"');
    }

    /**
     * 初始化PDO
     * 建立和db的連線
     * 
     * @return PDO
     */
    private function initializePDO() 
    {
        try {

            return new PDO(
                'mysql:host='.DB_SERVER.';dbname='.DB_DATABASE.';',
				DB_SERVER_USERNAME, 
			    DB_SERVER_PASSWORD
            );

        } catch (PDOException $e) {
            $this->errorLog($e);
            exit();
        }
    }

    /**
     * 交易: 開始交易
     * 
     * @return void
     */
    protected function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * 交易: 提交
     * 
     * @return void
     */
    protected function commit()
    {
        $this->pdo->commit();
    }

    /**
     * 交易: 復原
     * 
     * @return voic
     */
    protected function rollBack()
    {
        $this->pdo->rollBack();
    }

    /**
     * 交易: 是否在交易進行中
     * 
     * @return bool
     */
    protected function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    /**
     * error_log封裝
     * 
     * @param Exception $error
     * @return void
     */
    private function errorLog(Exception $error)
    {
        $message = ' [ stockPDO ] ' . $error;

        error_log($message);
    }

    /**
     * 驗證傳入的sql參數
     * 
     * @param string $sql
     * @return void
     */
    protected function validateSql($sql) 
    {
        if ( strlen(trim($sql)) === 0 ) {
            throw new Exception('SQL syntax is required');
        }
    }

    /**
     * 驗證參數是否在sql語句內
     * 
     * @param string $sql
     * @param array $param
     * @return void
     */
    protected function validateParamInSql($sql, $param)
    {
        foreach ($param as $key => $value) {
            $searchKey = preg_replace('/(^:|_\d$)/i', '', $key);

            if ( preg_match("/$searchKey\s+(=|>|<|like|in)/i", $sql) ) {
                continue;
            }

            // 判斷insert語句內的欄位
            if ( preg_match("/$searchKey(,|\s+\))/i", $sql) ) {
                continue;
            }

            throw new Exception( $key . 'is not found in SQL');
        }
    }

    /**
     * 將$sql內的變數替換成array中指定的值
     * 
     * @param PDOStatement $statement
     * @param array $param
     * @return string
     */
    protected function sqlBindParam($statement, $param)
    {
        foreach ( $param as $k => $v)
        {
            $statement->bindValue($k, $v);
        }

        return $statement;
    }

    /**
     * 確認已經執行完的sql語句是否有錯誤
     * 
     * @param  PDOStatement $statement
     * @return boolean
     */
    protected function checkError($statement)
    {
        $res = $statement->errorInfo();

        return $res[0] !== '00000';
    }

    /**
     * 執行sql語句
     * 
     * @param string $sql
     * @param array $param
     * @param boolean $isNonQuery 不是select語句 
     * @param boolean $isTransaction 是否為交易
     * @return int|PDOStatement
     */
    protected function execSqlStatement(
        $sql, $param = null, $isNonQuery = false, $isTransaction = false
    )
    {
        try
        {
            if ( $isTransaction ) {
                $this->beginTransaction();
            }

            $this->validateSql($sql);

            $statement = $this->pdo->prepare($sql);

            if ( $param ) {
                $this->validateParamInSql($sql, $param);
                $statement = $this->sqlBindParam($statement, $param);
            }

            $statement->execute();

            if ( $this->checkError($statement) ) {
                throw new Exception( implode(' ', $statement->errorInfo()) );
            }

            if ( $isNonQuery ) {
                $rowCount = $statement->rowCount();
                $this->lastInsertId = $this->pdo->lastInsertId();
            }

            if ( $isTransaction ) {
                $this->commit();
            }

            if ( $isNonQuery ) {
                return $rowCount;
            }

            return $statement;

        } catch (Exception $e) {

            echo $e;
            $this->errorLog($e);

            if ( $isTransaction ) {
                $this->rollBack();
                throw $e;
            }

            exit();
        }
    }

    /**
     * 用來整理並建立要執行的insert的sql語句
     * 回傳['sql' => string, 'param' => array]
     * 
     * @param $tableName
     * @param array
     * @return Array
     */
    protected function generateInsertSql($tableName, $param)
    {
        $sql = "INSERT INTO $tableName ( %s ) VALUES ( %s )";
        $colName = [];
        $resultParam = [];

        foreach($param as $k => $v)
        {
            $colName[] = "`$k`";
            $resultParam[":$k"] = $v;
        }

        $sql = sprintf(
            $sql, 
            implode(', ', $colName), 
            implode(', ', array_keys($resultParam))
        );

        return ['sql' => $sql, 'param' => $resultParam];
    }

    /**
     * 用來整理並建立要執行的update語句
     * 回傳['sql' => string, 'param' => array]
     * 
     * 目前條件只限定=
     * 
     * @param string $tableName
     * @param array $param
     * @param array|null $condition
     * @return array
     */
    protected function generateUpdateSql($tableName, $param, $condition=null)
    {
        $sql = "UPDATE $tableName SET  %s WHERE 1=1 %s";
        $updateValue = [];
        $filter = [];
        $resultParam = [];

        foreach($param as $k => $v)
        {
            $updateValue[] = "$k = :$k";
            $resultParam[":$k"] = $v;
        }

        if ( $condition ) {
            foreach ($condition as $k => $v)
            {
                $valueType = gettype($v);

                if ( $valueType === 'string' ) {
                    $filter[] = "AND $k = '$v'";
                }

                $filter[] = "AND $k = $v";
            }
        }

        $sql = sprintf(
            $sql, 
            implode(', ', $updateValue),
            implode(' ', $filter)
        );

        return ['sql' => $sql, 'param' => $resultParam];
    }

    /**
     * 用來建立delete的sql語句
     * 回傳['sql' => string, 'param' => array]
     * 目前條件只有and、=
     * 
     * @param $tableName
     * @param $condition
     * @return array
     */
    protected function generateDeleteSql($tableName, $condition)
    {
        $sql = "DELETE FROM $tableName WHERE %s";
        $formatValue = [];
        $resultParam = [];

        foreach ($condition as $k => $v) {
            $formatValue[] = "$k = :$k";
            $resultParam[":$k"] = $v;
        }

        $sql = sprintf($sql, implode('AND ', $formatValue));

        return ['sql' => $sql, 'param' => $resultParam];
    }

    /**
     * 用來執行select語句
     * 
     * @param string $sql
     * @param array|null $param
     * @return array
     */
    public function queryData($sql, $param=null)
    {
        $stmt = $this->execSqlStatement($sql, $param);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $data;
    }

    /**
     * 用來執行insert語句，傳入報表名、欄位[欄位名=>值,...]
     * 
     * @param string $tableName
     * @param array $param
     * @return int
     */
    public function insertData($tableName, $param, $isTransaction=false)
    {
        $sqlData = $this->generateInsertSql($tableName, $param);

        $rowCount = $this->execSqlStatement(
            $sqlData['sql'], $sqlData['param'], true, $isTransaction
        );

        return (int) $rowCount;
    }

    /**
     * 用來執行updateTable，傳入報表名、欄位所需改的數值[欄位名=> 值, ...]、
     * 條件[欄位名 => 值]
     * 條件目前只有=
     * 
     * @param string $tableName
     * @param array $param
     * @param array $condition
     * @param boolean $isTransaction
     * @return int
     */
    public function updateData($tableName, $param, $condition, $isTransaction=false)
    {
        if ( tools_isNull($param) || tools_isNull($condition) ) {
            return 0;
        }

        $sqlData = $this->generateUpdateSql($tableName, $param, $condition);

        $rowCount = $this->execSqlStatement(
            $sqlData['sql'], $sqlData['param'], true, $isTransaction
        );

        return $rowCount;
    }

    /**
     * 用來執行delete，傳入報表名，及欄位條件[欄位名=> 值, ...]
     * 刪除符合條件的資料
     * 回傳被刪掉的筆數
     * 
     * @param string $tableName
     * @param array $param
     * @param boolean $isTransaction
     * @return int
     */
    public function deleteData($tableName, $param, $isTransaction=false)
    {
        if ( tools_isNull($param) ) {
            return 0;
        }

        $sqlData = $this->generateDeleteSql($tableName, $param);

        $rowCount = $this->execSqlStatement(
            $sqlData['sql'], $sqlData['param'], true, $isTransaction
        );

        return $rowCount;
    }

    /**
     * createTable
     * 
     * @param string $sql
     * @param void
     */
    public function createTable($sql)
    {
        $this->execSqlStatement($sql);
    }

    /**
     * 用來執行刪除table
     * 
     * @param $tableName
     * @return void
     */
    public function dropTable($tableName)
    {
        $sql = "DROP TABLE $tableName";

        $this->execSqlStatement($sql);
    }

    /**
     * 用來查看目前有哪些報表
     * 
     * @return void
     */
    public function getTables()
    {
        $sql = 'SHOW TABLES';

        $stat = $this->execSqlStatement($sql);

        $data = $stat->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * 用來取得最後一筆資料的日期
     * 
     * @param $tableName
     * @return string
     */
    public function getLastRowDate($tableName)
    {
        $sql = "SELECT SUBSTR(`date`, 1, 10) AS `date` FROM $tableName ORDER BY `id` DESC LIMIT 1";

        $date = $this->queryData($sql);

        return $date[0]['date'];
    }

}