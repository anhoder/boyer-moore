PHP实现的Boyer-Moore字符搜索算法，支持中文。
PHP implementation of Boyer-Moore character search algorithm, support for Chinese.

![GitHub repo size](https://img.shields.io/github/repo-size/anhoder/boyer-moore) ![GitHub](https://img.shields.io/github/license/anhoder/boyer-moore) ![Last Tag](https://badgen.net/github/tag/anhoder/boyer-moore) ![GitHub last commit](https://badgen.net/github/last-commit/anhoder/boyer-moore) ![GitHub All Releases](https://img.shields.io/github/downloads/anhoder/boyer-moore/total)

![GitHub stars](https://img.shields.io/github/stars/anhoder/boyer-moore?style=social) ![GitHub forks](https://img.shields.io/github/forks/anhoder/boyer-moore?style=social)

## Requirement

```php
"symfony/polyfill-mbstring": "^1.23"
```

## Install

```shell
composer require anhoder/boyer-moore
```

## Usage

```php
require './vendor/autoload.php';

$text = 'ababa';
$matcher = new \Anhoder\Matcher\BoyerMooreMatcher('aba');
$res = $matcher->match($text, \Anhoder\Matcher\BoyerMooreMatcher::MODE_REUSE_MATCHED);
var_dump($res);
```

* `BoyerMooreMatcher::MODE_ONLY_ONE`: 匹配到一个就返回
* `BoyerMooreMatcher::MODE_SKIP_MATCHED`: 找出所有匹配的字串，已匹配的字符不参与后续匹配，例如：在`ababa`中搜索`aba`结果为`[0]`
* `BoyerMooreMatcher::MODE_SKIP_MATCHED`: 找出所有匹配的字串，已匹配字符继续参与匹配，例如：在`ababa`中搜索`aba`结果为`[0, 2]`

