<?php
/**
 * The file is part of the boyer-moore.
 *
 * (c) anhoder <anhoder@88.cn>.
 *
 * 2021/9/4 12:59 上午
 */

use Anhoder\Matcher\BoyerMooreMatcher;
use PHPUnit\Framework\TestCase;

class BoyerMooreMatcherTest extends TestCase
{
    /**
     * @covers BoyerMooreMatcher::match
     * @covers BoyerMooreMatcher::buildBadCharTable
     * @covers BoyerMooreMatcher::buildGoodSuffixTable
     * @covers BoyerMooreMatcher::suffixes
     */
    public function testMatch()
    {
        $text = 'In computer science, the Boyer–Moore string-search algorithm is an efficient string-searching algorithm that is the standard benchmark for practical string-search literature.[1] It was developed by Robert S. Boyer and J Strother Moore in 1977.[2] The original paper contained static tables for computing the pattern shifts without an explanation of how to produce them. The algorithm for producing the tables was published in a follow-on paper; this paper contained errors which were later corrected by Wojciech Rytter in 1980.[3][4] The algorithm preprocesses the string being searched for (the pattern), but not the string being searched in (the text). It is thus well-suited for applications in which the pattern is much shorter than the text or where it persists across multiple searches. The Boyer–Moore algorithm uses information gathered during the preprocess step to skip sections of the text, resulting in a lower constant factor than many other string search algorithms. In general, the algorithm runs faster as the pattern length increases. The key features of the algorithm are to match on the tail of the pattern rather than the head, and to skip along the text in jumps of multiple characters rather than searching every single character in the text.';
        $pattern = 'search';
        $matcher = new BoyerMooreMatcher($pattern);
        $res = $matcher->match($text, BoyerMooreMatcher::MODE_SKIP_MATCHED);
        var_dump($res);

        $this->assertCount(8, $res);
    }

    /**
     * @covers BoyerMooreMatcher::match
     * @covers BoyerMooreMatcher::buildBadCharTable
     * @covers BoyerMooreMatcher::buildGoodSuffixTable
     * @covers BoyerMooreMatcher::suffixes
     */
    public function testOnlyOneMode()
    {
        $text = 'abababababbababaabaabababaaababab';
        $pattern = 'bab';
        $matcher = new BoyerMooreMatcher($pattern);
        $res = $matcher->match($text, BoyerMooreMatcher::MODE_ONLY_ONE);
        var_dump($res);

        $this->assertEquals([1], $res);
    }

    /**
     * @covers BoyerMooreMatcher::match
     * @covers BoyerMooreMatcher::buildBadCharTable
     * @covers BoyerMooreMatcher::buildGoodSuffixTable
     * @covers BoyerMooreMatcher::suffixes
     */
    public function testSkipMatchedMode()
    {
        $text = 'abababababbababaabaabababaaababab';
        $pattern = 'bab';
        $matcher = new BoyerMooreMatcher($pattern);
        $res = $matcher->match($text, BoyerMooreMatcher::MODE_SKIP_MATCHED);
        var_dump($res);

        $this->assertEquals([1, 5, 10, 20, 28], $res);
    }

    /**
     * @covers BoyerMooreMatcher::match
     * @covers BoyerMooreMatcher::buildBadCharTable
     * @covers BoyerMooreMatcher::buildGoodSuffixTable
     * @covers BoyerMooreMatcher::suffixes
     */
    public function testReuseMatchedMode()
    {
        $text = 'abababababbababaabaabababaaababab';
        $pattern = 'bab';
        $matcher = new BoyerMooreMatcher($pattern);
        $res = $matcher->match($text, BoyerMooreMatcher::MODE_REUSE_MATCHED);
        var_dump($res);

        $this->assertEquals([1, 3, 5, 7, 10, 12, 20, 22, 28, 30], $res);
    }

    /**
     * @covers BoyerMooreMatcher::match
     * @covers BoyerMooreMatcher::buildBadCharTable
     * @covers BoyerMooreMatcher::buildGoodSuffixTable
     * @covers BoyerMooreMatcher::suffixes
     */
    public function testUnicode()
    {
        $text = '好😂好😂好😂好😂好😂😂好😂撒😂算😂法';
        $pattern = '好😂好';
        $matcher = new BoyerMooreMatcher($pattern);
        $res = $matcher->match($text, BoyerMooreMatcher::MODE_REUSE_MATCHED);
        var_dump($res);

        $this->assertEquals([0, 2, 4, 6], $res);
    }
}
