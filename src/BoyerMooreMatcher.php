<?php
/**
 * The file is part of the boyer-moore.
 *
 * (c) anhoder <anhoder@88.cn>.
 *
 * 2021/9/4 12:47 上午
 */

namespace Anhoder\Matcher;

use RuntimeException;

/**
 * class BoyerMooreMatcher.
 */
class BoyerMooreMatcher
{
    public const MODE_ONLY_ONE      = 1; // 只匹配一个
    public const MODE_SKIP_MATCHED  = 2; // 跳过已匹配的字符
    public const MODE_REUSE_MATCHED = 3; // 已匹配字符可重复匹配

    /**
     * @var array
     */
    private $pattern;

    /**
     * @var array
     */
    private $badCharTable = [];

    /**
     * @var array
     */
    private $goodSuffixTable = [];

    /**
     * BoyerMooreSearcher constructor.
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->setPattern($pattern);
    }

    /**
     * Set pattern.
     * @param string $pattern
     * @return void
     */
    public function setPattern(string $pattern)
    {
        if (!function_exists('mb_str_split')) {
            throw new RuntimeException('Function mb_str_split not exists! Please make sure that the PHP version >=7.4 or install the composer package `symfony/polyfill-mbstring`.');
        }
        $this->pattern = mb_str_split($pattern);

        // build bad char table
        $this->buildBadCharTable();

        // build good suffix table
        $this->buildGoodSuffixTable();
    }

    /**
     * Get pattern.
     * @return array
     */
    public function getPattern(): array
    {
        return $this->pattern;
    }

    /**
     * Find bad chars from pattern.
     * @return void
     */
    private function buildBadCharTable()
    {
        $len = count($this->pattern);

        for ($i = 0; $i < $len -1; ++$i) {
            $this->badCharTable[$this->pattern[$i]] = $len - $i - 1;
        }

        $this->badCharTable[$this->pattern[$len-1]] = 1; // 最右边字符为1，避免原地踏步
    }

    /**
     * Find good suffixes from pattern.
     * @return void
     */
    private function buildGoodSuffixTable()
    {
        $len = count($this->pattern);

        $suffixes = $this->suffixes();

        for ($i = 0; $i < $len; ++$i) {
            $this->goodSuffixTable[$i] = $len;
        }

        for ($i = $len - 1; $i >= -1; --$i) {
            if ($i != -1 && $suffixes[$i] != $i + 1) {
                continue;
            }

            for ($j = 0; $j < $len - $i - 1; ++$j) {
                if ($this->goodSuffixTable[$j] == $len) {
                    $this->goodSuffixTable[$j] = $len - $i - 1;
                }
            }
        }

        for ($i = 0; $i < $len - 1; ++$i) {
            $this->goodSuffixTable[$len - 1 - $suffixes[$i]] = $len - $i - 1;
        }
    }

    /**
     * Get suffixes.
     * @return array
     * [
     *     index => suffix_length
     * ]
     */
    private function suffixes(): array
    {
        $len = count($this->pattern);

        $suffixes[$len - 1] = $len;
        $cursor = $len - 1; // 匹配后缀时使用的游标
        $matchedIndex = 0;  // 上次匹配后缀时的位置i

        for ($i = $len - 2; $i >= 0; --$i) {
            // 如果上次已匹配后缀包含当前位置, 说明该字符重复出现, 使用已有的后缀长度
            if ($i > $cursor && $suffixes[$i + $len - 1 - $matchedIndex] < $i - $cursor) {
                $suffixes[$i] = $suffixes[$i + $len - 1 - $matchedIndex];
            } else {
                if ($i < $cursor) {
                    $cursor = $i;
                }
                $matchedIndex = $i;

                // 获取匹配后缀的长度
                while ($cursor >= 0 && $this->pattern[$cursor] == $this->pattern[$cursor + $len - 1 - $matchedIndex]) {
                    $cursor--;
                }
                $suffixes[$i] = $matchedIndex - $cursor;
            }
        }

        return $suffixes;
    }

    /**
     * Match.
     * @param string $text
     * @param int $matchMode
     * @return array
     */
    public function match(string $text, int $matchMode = self::MODE_ONLY_ONE): array
    {
        $text = mb_str_split($text);

        $res = [];
        $textLen = count($text);
        $patternLen = count($this->pattern);

        if ($patternLen > $textLen) {
            return [];
        }

        for ($i = 0; $i <= $textLen - $patternLen;) {
            $cursor = $patternLen - 1;
            while ($cursor >= 0 && $this->pattern[$cursor] == $text[$i + $cursor]) {
                --$cursor;
            }
            if ($cursor >= 0) {
                // 不匹配
                $steps = max($this->goodSuffixTable[$cursor], ($this->badCharTable[$text[$i + $cursor]] ?? $patternLen) - $patternLen + 1 + $cursor);
            } else {
                $res[] = $i;

                if ($matchMode == self::MODE_ONLY_ONE) {
                    return $res;
                }

                switch ($matchMode) {
                    case self::MODE_ONLY_ONE:
                        return $res;
                    case self::MODE_SKIP_MATCHED:
                        $steps = $patternLen;
                        break;
                    case self::MODE_REUSE_MATCHED:
                        $steps = $this->goodSuffixTable[0];
                        break;
                    default:
                        throw new RuntimeException("Dont support this match mode({$matchMode})");
                }
            }

            $i += ($steps >= 0 ? $steps : 1);
        }

        return $res;
    }
}
