<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\Config\Config;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\ArrayUtils\MergeRemoveKey;

class ArrayUtilsTest extends TestCase
{
    public static function validHashTables()
    {
        return array(
            array(array(
                'foo' => 'bar'
            )),
            array(array(
                '15',
                'foo' => 'bar',
                'baz' => array('baz')
            )),
            array(array(
                0 => false,
                2 => null
            )),
            array(array(
                -100 => 'foo',
                100  => 'bar'
            )),
            array(array(
                1 => 0
            )),
        );
    }

    public static function validLists()
    {
        return array(
            array(array(null)),
            array(array(true)),
            array(array(false)),
            array(array(0)),
            array(array(-0.9999)),
            array(array('string')),
            array(array(new stdClass)),
            array(array(
                0 => 'foo',
                1 => 'bar',
                2 => false,
                3 => null,
                4 => array(),
                5 => new stdClass()
            ))
        );
    }

    public static function validArraysWithStringKeys()
    {
        return array(
            array(array(
                'foo' => 'bar',
            )),
            array(array(
                'bar',
                'foo' => 'bar',
                'baz',
            )),
        );
    }

    public static function validArraysWithNumericKeys()
    {
        return array(
            array(array(
                'foo',
                'bar'
            )),
            array(array(
                '0' => 'foo',
                '1' => 'bar',
            )),
            array(array(
                'bar',
                '1' => 'bar',
                 3  => 'baz'
            )),
            array(array(
                -10000   => null,
                '-10000' => null,
            )),
            array(array(
                '-00000.00009' => 'foo'
            )),
            array(array(
                1 => 0
            )),
        );
    }

    public static function validArraysWithIntegerKeys()
    {
        return array(
            array(array(
                'foo',
                'bar,'
            )),
            array(array(
                100 => 'foo',
                200 => 'bar'
            )),
            array(array(
                -100 => 'foo',
                0    => 'bar',
                100  => 'baz'
            )),
            array(array(
                'foo',
                'bar',
                1000 => 'baz'
            )),
        );
    }

    public static function invalidArrays()
    {
        return array(
            array(new stdClass()),
            array(15),
            array('foo'),
            array(new ArrayObject()),
        );
    }

    public static function mergeArrays()
    {
        return array(
            'merge-integer-and-string-keys' => array(
                array(
                    'foo',
                    3     => 'bar',
                    'baz' => 'baz',
                    4     => array(
                        'a',
                        1 => 'b',
                        'c',
                    ),
                ),
                array(
                    'baz',
                    4 => array(
                        'd' => 'd',
                    ),
                ),
                false,
                array(
                    0     => 'foo',
                    3     => 'bar',
                    'baz' => 'baz',
                    4     => array(
                        'a',
                        1 => 'b',
                        'c',
                    ),
                    5     => 'baz',
                    6     => array(
                        'd' => 'd',
                    ),
                )
            ),
            'merge-integer-and-string-keys-preserve-numeric' => array(
                array(
                    'foo',
                    3     => 'bar',
                    'baz' => 'baz',
                    4     => array(
                        'a',
                        1 => 'b',
                        'c',
                    ),
                ),
                array(
                    'baz',
                    4 => array(
                        'd' => 'd',
                    ),
                ),
                true,
                array(
                    0     => 'baz',
                    3     => 'bar',
                    'baz' => 'baz',
                    4 => array(
                        'a',
                        1 => 'b',
                        'c',
                        'd' => 'd',
                    ),
                )
            ),
            'merge-arrays-recursively' => array(
                array(
                    'foo' => array(
                        'baz'
                    )
                ),
                array(
                    'foo' => array(
                        'baz'
                    )
                ),
                false,
                array(
                    'foo' => array(
                        0 => 'baz',
                        1 => 'baz'
                    )
                )
            ),
            'replace-string-keys' => array(
                array(
                    'foo' => 'bar',
                    'bar' => array()
                ),
                array(
                    'foo' => 'baz',
                    'bar' => 'bat'
                ),
                false,
                array(
                    'foo' => 'baz',
                    'bar' => 'bat'
                )
            ),
            'merge-with-null' => array(
                array(
                    'foo' => null,
                    null  => 'rod',
                    'cat' => 'bar',
                    'god' => 'rad'
                ),
                array(
                    'foo' => 'baz',
                    null  => 'zad',
                    'god' => null
                ),
                false,
                array(
                    'foo' => 'baz',
                    null  => 'zad',
                    'cat' => 'bar',
                    'god' => null
                )
            ),
        );
    }

    /**
     * @group 6903
     */
    public function testMergeReplaceKey()
    {
        $expected = array(
            'car' => array(
                'met' => 'bet',
            ),
            'new' => array(
                'foo' => 'get',
            ),
        );
        $a = array(
            'car' => array(
                'boo' => 'foo',
                'doo' => 'moo',
            ),
        );
        $b = array(
            'car' => new \Zend\Stdlib\ArrayUtils\MergeReplaceKey(array(
                'met' => 'bet',
            )),
            'new' => new \Zend\Stdlib\ArrayUtils\MergeReplaceKey(array(
                'foo' => 'get',
            )),
        );
        $this->assertInstanceOf('Zend\Stdlib\ArrayUtils\MergeReplaceKeyInterface', $b['car']);
        $this->assertEquals($expected, ArrayUtils::merge($a, $b));
    }

    /**
     * @group 6899
     */
    public function testAllowsRemovingKeys()
    {
        $a = array(
            'foo' => 'bar',
            'bar' => 'bat'
        );
        $b = array(
            'foo' => new MergeRemoveKey(),
            'baz' => new MergeRemoveKey(),
        );
        $expected = array(
            'bar' => 'bat'
        );
        $this->assertEquals($expected, ArrayUtils::merge($a, $b));
    }

    public static function validIterators()
    {
        return array(
            array(array(
                'foo' => 'bar',
            ), array(
                'foo' => 'bar',
            )),
            array(new Config(array(
                'foo' => array(
                    'bar' => array(
                        'baz' => array(
                            'baz' => 'bat',
                        ),
                    ),
                ),
            )), array(
                'foo' => array(
                    'bar' => array(
                        'baz' => array(
                            'baz' => 'bat',
                        ),
                    ),
                ),
            )),
            array(new ArrayObject(array(
                'foo' => array(
                    'bar' => array(
                        'baz' => array(
                            'baz' => 'bat',
                        ),
                    ),
                ),
            )), array(
                'foo' => array(
                    'bar' => array(
                        'baz' => array(
                            'baz' => 'bat',
                        ),
                    ),
                ),
            )),
        );
    }

    public static function invalidIterators()
    {
        return array(
            array(null),
            array(true),
            array(false),
            array(0),
            array(1),
            array(0.0),
            array(1.0),
            array('string'),
            array(new stdClass),
        );
    }

    /**
     * @dataProvider validArraysWithStringKeys
     */
    public function testValidArraysWithStringKeys($test)
    {
        $this->assertTrue(ArrayUtils::hasStringKeys($test));
    }

    /**
     * @dataProvider validArraysWithIntegerKeys
     */
    public function testValidArraysWithIntegerKeys($test)
    {
        $this->assertTrue(ArrayUtils::hasIntegerKeys($test));
    }

    /**
     * @dataProvider validArraysWithNumericKeys
     */
    public function testValidArraysWithNumericKeys($test)
    {
        $this->assertTrue(ArrayUtils::hasNumericKeys($test));
    }

    /**
     * @dataProvider invalidArrays
     */
    public function testInvalidArraysAlwaysReturnFalse($test)
    {
        $this->assertFalse(ArrayUtils::hasStringKeys($test, false));
        $this->assertFalse(ArrayUtils::hasIntegerKeys($test, false));
        $this->assertFalse(ArrayUtils::hasNumericKeys($test, false));
        $this->assertFalse(ArrayUtils::isList($test, false));
        $this->assertFalse(ArrayUtils::isHashTable($test, false));

        $this->assertFalse(ArrayUtils::hasStringKeys($test, false));
        $this->assertFalse(ArrayUtils::hasIntegerKeys($test, false));
        $this->assertFalse(ArrayUtils::hasNumericKeys($test, false));
        $this->assertFalse(ArrayUtils::isList($test, false));
        $this->assertFalse(ArrayUtils::isHashTable($test, false));
    }

    /**
     * @dataProvider validLists
     */
    public function testLists($test)
    {
        $this->assertTrue(ArrayUtils::isList($test));
        $this->assertTrue(ArrayUtils::hasIntegerKeys($test));
        $this->assertTrue(ArrayUtils::hasNumericKeys($test));
        $this->assertFalse(ArrayUtils::hasStringKeys($test));
        $this->assertFalse(ArrayUtils::isHashTable($test));
    }

    /**
     * @dataProvider validHashTables
     */
    public function testHashTables($test)
    {
        $this->assertTrue(ArrayUtils::isHashTable($test));
        $this->assertFalse(ArrayUtils::isList($test));
    }

    public function testEmptyArrayReturnsTrue()
    {
        $test = array();
        $this->assertTrue(ArrayUtils::hasStringKeys($test, true));
        $this->assertTrue(ArrayUtils::hasIntegerKeys($test, true));
        $this->assertTrue(ArrayUtils::hasNumericKeys($test, true));
        $this->assertTrue(ArrayUtils::isList($test, true));
        $this->assertTrue(ArrayUtils::isHashTable($test, true));
    }

    public function testEmptyArrayReturnsFalse()
    {
        $test = array();
        $this->assertFalse(ArrayUtils::hasStringKeys($test, false));
        $this->assertFalse(ArrayUtils::hasIntegerKeys($test, false));
        $this->assertFalse(ArrayUtils::hasNumericKeys($test, false));
        $this->assertFalse(ArrayUtils::isList($test, false));
        $this->assertFalse(ArrayUtils::isHashTable($test, false));
    }

    /**
     * @dataProvider mergeArrays
     */
    public function testMerge($a, $b, $preserveNumericKeys, $expected)
    {
        $this->assertEquals($expected, ArrayUtils::merge($a, $b, $preserveNumericKeys));
    }

    /**
     * @dataProvider validIterators
     */
    public function testValidIteratorsReturnArrayRepresentation($test, $expected)
    {
        $result = ArrayUtils::iteratorToArray($test);
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider invalidIterators
     */
    public function testInvalidIteratorsRaiseInvalidArgumentException($test)
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException');
        $this->assertFalse(ArrayUtils::iteratorToArray($test));
    }

    public function filterArrays()
    {
        return array(
            array(
                array('foo' => 'bar', 'fiz' => 'buz'),
                function ($value) {
                    if ($value == 'bar') {
                        return false;
                    }
                    return true;
                },
                null,
                array('fiz' => 'buz')
            ),
            array(
                array('foo' => 'bar', 'fiz' => 'buz'),
                function ($value, $key) {
                    if ($value == 'buz') {
                        return false;
                    }

                    if ($key == 'foo') {
                        return false;
                    }

                    return true;
                },
                ArrayUtils::ARRAY_FILTER_USE_BOTH,
                array()
            ),
            array(
                array('foo' => 'bar', 'fiz' => 'buz'),
                function ($key) {
                    if ($key == 'foo') {
                        return false;
                    }
                    return true;
                },
                ArrayUtils::ARRAY_FILTER_USE_KEY,
                array('fiz' => 'buz')
            ),
        );
    }

    /**
     * @dataProvider filterArrays
     */
    public function testFiltersArray($data, $callback, $flag, $result)
    {
        $this->assertEquals($result, ArrayUtils::filter($data, $callback, $flag));
    }

    /**
     * @expectedException \Zend\Stdlib\Exception\InvalidArgumentException
     */
    public function testInvalidCallableRaiseInvalidArgumentException()
    {
        ArrayUtils::filter(array(), "INVALID");
    }
}
