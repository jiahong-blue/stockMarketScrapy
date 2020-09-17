<?php
require_once __DIR__ .'/testCase.class.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'util/stockHtmlParse.class.php';

$testCase = new TestCase(new HTMLParse());

/**
 * parseBlockElement
 */
$testCase->testMethod(
    'parseBlockElement',
    [
        'predictValue' => '<div>aeff</div> <div><div></div></div>', 
        'predictType' => 'string', 
        'param' => [
            "<h1>title</h1><div>aeff</div> <div><div></div></div>",
            'div'
        ]
    ],
    '測抓'. htmlspecialchars("<h1>title</h1><div>aeff</div> <div><div></div></div>")
);
$testCase->testMethod(
    'parseBlockElement',
    [
        'predictValue' => '', 
        'predictType' => 'string', 
        'param' => [
            "<h1>title</h1><div>aeff</div> <div><div></div></div>",
            'abc'
        ]
    ],
    '測abc tag抓'. htmlspecialchars("<h1>title</h1><div>aeff</div> <div><div></div></div>")
);
$testCase->testMethod(
    'parseBlockElement',
    [
        'predictValue' => '', 
        'predictType' => 'string', 
        'param' => [
            "<h1>title</h1><div>aeff</div> <div><div></div></div>",
            []
        ]
    ],
    '測[]抓'. htmlspecialchars("<h1>title</h1><div>aeff</div> <div><div></div></div>")
);
$testCase->testMethod(
    'parseBlockElement',
    [
        'predictValue' => '', 
        'predictType' => 'string', 
        'param' => [
            "<h1>title</h1><div>aeff</div><p>test p<div><div></div></div>",
            'p'
        ]
    ],
    '測p抓'. htmlspecialchars("<h1>title</h1><div>aeff</div> <div><div></div></div>")
);

/**
 * parseInlineElement
 */
$testCase->testMethod(
    'parseInlineElement',
    [
        'predictValue' => [], 
        'predictType' => 'array', 
        'param' => [
            "<h1>title</h1><div>aeff</div><p>test </p><img href=''><div><div></div></div>",
            'p'
        ]
    ],
    '測p(block)'
);
$testCase->testMethod(
    'parseInlineElement',
    [
        'predictValue' => [], 
        'predictType' => 'array', 
        'param' => [
            "<h1>title</h1><div>aeff</div><p>test </p><img href=''><div><div></div></div>",
            'img'
        ]
    ],
    '測img(inline但是empty)'
);
$testCase->testMethod(
    'parseInlineElement',
    [
        'predictValue' => ["<a>test \n link<\/a>"], 
        'predictType' => 'array', 
        'param' => [
            "<h1>title</h1><div>aeff</div><p>test </p><img href=''><a>test \n link</a><div><div></div></div>",
            'a'
        ]
    ],
    '測a,但回傳的值帶html tag，只能觀察'
);

/**
 * parseEmptyElement
 */
$testCase->testMethod(
    'parseEmptyElement',
    [
        'predictValue' => ["<img href=''>"], 
        'predictType' => 'array', 
        'param' => [
            "<h1>title</h1><div>aeff</div><p>test </p><img href=''><a>test \n link</a><div><div></div></div>",
            'img'
        ]
    ],
    '測img'
);

$testCase->testMethod(
    'parseEmptyElement',
    [
        'predictValue' => [], 
        'predictType' => 'array', 
        'param' => [
            "<h1>title</h1><div>aeff</div><p>test </p><img href=''><a>test \n link</a><div><div></div></div>",
            'div'
        ]
    ],
    '測div'
);
$testCase->testMethod(
    'parseEmptyElement',
    [
        'predictValue' => [], 
        'predictType' => 'array', 
        'param' => [
            "<h1>title</h1><div>aeff</div><p>test </p><imga href=''><a>test \n link</a><div><div></div></div>",
            'div'
        ]
    ],
    '測img，傳入的內容沒img'
);

/**
 * parseContent
 */
$testCase->testMethod(
    'parseContent',
    [
        'predictValue' => 'test title', 
        'predictType' => 'string', 
        'param' => [
            "<h1>test title</h1>",
        ]
    ],
    '測parseContent，h1'
);
$testCase->testMethod(
    'parseContent',
    [
        'predictValue' => '<p>first paragraph</p> <h1>test title</h1>', 
        'predictType' => 'string', 
        'param' => [
            "<div><p>first paragraph</p> <h1>test title</h1></div>",
        ]
    ],
    '測parseContent，div'
);

/**
 * parseElement
 */
$testCase->testMethod(
    'parseElement',
    [
        'predictValue' => ["<h1>test title<\/h1>"], 
        'predictType' => 'array', 
        'param' => [
            "<div><p>first paragraph</p> <h1>test title</h1></div>",
            'h1'
        ]
    ],
    '測parseElement，h1，觀察會對'
);
$testCase->testMethod(
    'parseElement',
    [
        'predictValue' => ["<p>first paragraph</p>"], 
        'predictType' => 'array', 
        'param' => [
            "<div><p>first paragraph</p> <h1>test title</h1></div>",
            'p'
        ]
    ],
    '測parseElement，p，觀察會對'
);
$testCase->testMethod(
    'parseElement',
    [
        'predictValue' => [
            "<p>first paragraph</p>",
            "<p>second paragraph</p>",
            "<p>third paragraph</p>"
        ], 
        'predictType' => 'array', 
        'param' => [
            "<div><p>first paragraph</p> <p>second paragraph</p> <p>third paragraph</p><h1>test title</h1></div>",
            'p'
        ]
    ],
    '測parseElement，p，觀察會對'
);