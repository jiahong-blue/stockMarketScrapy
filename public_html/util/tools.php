<?php

/**
 * 用來確認是否為空，原生並不把空陣列當null
 * 此function也將[]、0、'0'當null
 * 
 * @param any $value
 * @return boolean
 */
function tools_isNull($value)
{
    $valueType = gettype($value);

    switch ( $valueType ) {
        case 'array':
            return $value === [];
            break;
        case 'string':
            $res = trim($value) === '' || $value === '0';
            return $res;
        case 'integer':
            return $value === 0;
        default:
            return is_null($value);
    }

}

/**
 * 用來測試使否為string且不為''、' '
 * '0'還是被視為string
 * 
 * @param any $value
 * @return boolean
 */
function tools_isStringExceptNull($value)
{
    return gettype($value) === 'string' && trim($value) !== '';
}

/**
 * 用來測試是否為array且不為[]
 * 
 * @param any $value
 * @return boolean
 */
function tools_isArrayExceptNull($value)
{
    return gettype($value) === 'array' && $value !== [];
}

/**
 * 用來驗證是否為0或1
 * 
 * @param any $value
 * @return boolean
 */
function tools_isOneOrZero($value)
{
    return $value === 0 || $value === 1;
}

/**
 * 用來確認日期，目前僅支援Y-m-d
 * 
 * @param string $date
 * @return boolean
 */
function tools_validateDate($date)
{
    if ( preg_match('/\d{4}-\d{2}-d\{2}/', $date, $match) ) {
        return false;
    }

    $dateArray = explode('-', $date);

    return checkdate($dateArray[1], $dateArray[2], $dateArray[0]);
}