<?php
require_once __DIR__ . '/tools.php';
/**
 * 用來處理Html文本
 * 目前只提供功能
 * 
 */
class HTMLParse
{
    /**
     * block element
     * 參考https://www.w3schools.com/html/html_blocks.asp
     */
    const BLOCK_ELEMENT = ['address', 'article', 'aside', 'blockquote', 
        'canvas', 'dd', 'div', 'dl', 'dt', 'fieldset', 'figcaption', 
        'figure', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 
        'header', 'hr', 'li', 'main', 'nav', 'noscript', 'ol', 'p', 'pre', 
        'section', 'table', 'tfoot', 'ul', 'video'];

    /**
     * inline element
     */
    const INLINE_ELEMENT = ['a', 'abbr', 'acronym', 'b', 'bdo', 'big', 'br', 
        'button', 'cite', 'code', 'dfn', 'em', 'i', 'img', 'input', 'kbd', 
        'label', 'map', 'object', 'output', 'q', 'samp', 'script', 'select', 
        'small', 'span', 'strong', 'sub', 'sup', 'textarea', 'time', 'tt', 
        'var'];

    /**
     * empty element
     */
    const EMPTY_ELEMENT = ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 
        'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'];
    
    /**
     * 目前只提供功能
     */
    public function __construct()
    {

    }

    /**
     * 驗證傳入的tag是否為block element
     * 
     * @param string $tag
     * @return boolean
     */
    protected function validateBlockTag($tag) {
        return in_array($tag, self::BLOCK_ELEMENT);
    }

    /**
     * 驗證傳入的tag是否為inline element
     * 
     * @param string $tag
     * @param boolean
     */
    protected function validateInlineElement($tag)
    {
        return in_array($tag, self::INLINE_ELEMENT);
    }

    /**
     * 驗證傳入的tag是否為empty element，也就是沒有end tag
     * 
     * @param string $tag
     * @return boolean
     */
    protected function validateEmptyElement($tag)
    {
        return in_array($tag, self::EMPTY_ELEMENT);
    }

    /**
     * 用來解析block element，且是有end tag的element
     * 目前解析最遠距離
     * 例如:'<h1>測試標題</h1><div><p>外部</p><div><p>內部</p></div></div>'
     * 會取到'<div><p>外部</p><div><p>內部</p></div></div>'
     * 
     * @param string $content
     * @param string $tag
     * @return string
     */
    public function parseBlockElement($content, $tag)
    {
        $check = tools_isStringExceptNull($content) &&
                 $this->validateBlockTag($tag) &&
                 !$this->validateEmptyElement($tag);

        if ( !$check ) {
            return '';
        }

        // 找最開始
        $start = stripos($content, "<$tag");

        if ( $start === false ) {
            return '';
        }

        $end = strripos($content, "</$tag>");

        if ( $end === false ) {
            return '';
        }
        $end = $end + strlen("</$tag>");

        return substr($content, $start, $end - $start);
    }

    /**
     * 抓inline element且有end tag
     * 參考 https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
     * 
     * @param string $content
     * @param string $tag
     * @return array
     */
    public function parseInlineElement($content, $tag)
    {
        $check = tools_isStringExceptNull($content) &&
                 $this->validateInlineElement($tag) &&
                 !$this->validateEmptyElement($tag);

        if ( !$check ) {
            return [];
        }

        $pattern = "|<$tag.*>(.*?)</$tag>|s";
        preg_match_all($pattern, $content, $matches, PREG_PATTERN_ORDER);
 
        return $matches[0];
    }

    /**
     * 抓沒有end tag的element
     * 
     * @param string $content
     * @param string $tag
     * @return array
     */
    public function parseEmptyElement($content, $tag)
    {
        $check = tools_isStringExceptNull($content) &&
                 $this->validateEmptyElement($tag);
        
        if ( !$check ) {
            return [];
        }

        preg_match_all("/<$tag\s+.*?>/s", $content, $matches, PREG_PATTERN_ORDER);

        return $matches[0];
    }

    /**
     * 取得element content
     * 例如:<p>text</p>
     * 得到text
     * 
     * @param string $element
     * @return string
     */
    public function parseContent($element)
    {
        if ( !tools_isStringExceptNull($element) ) {
            return '';
        }

        preg_match('/(?<=>).*(?=<)/s', $element, $matches);

        return  $matches[0];
    }

    /**
     * 取得非特定的tag
     * 例如: tr、td
     * 
     * @param string $content
     * @param string $tag
     * @return array
     */
    public function parseElement($content, $tag)
    {
        preg_match_all("|<$tag.*?>(.*?)</$tag>|is", $content, $matches, PREG_PATTERN_ORDER);

        return $matches;
    }

    /**
     * 移除tag的符號，例如:<...>
     * 
     * @param string|array $content
     * @return string|array
     */
    public function removeAllTag($content)
    {
        return preg_replace('/<.*?>/s', '', $content);
    }
    /**
     * 清掉、換行
     * \r\n - on a windows computer
     * \r - on an Apple computer
     * \n - on Linux
     * 
     * @param string $content
     * @return string
     */
    public function removeNewline($content)
    {
        return preg_replace('/[\r|\n|\r\n]+/', '', $content);
    }

    /**
     * 移除空白
     * 
     * @param string|array $content
     * @return string|array
     */
    public function removeWhitespace($content)
    {
        return preg_replace('/\s+/', '', $content);
    }
}