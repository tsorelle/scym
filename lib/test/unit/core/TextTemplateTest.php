<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/24/2015
 * Time: 7:44 AM
 */

namespace Tops\test\unit\core;



use Tops\sys\TTextTemplate;

class TextTemplateTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge() {
        $template ="{{greeting}} {{planet}}";
        $tokens = array(
            "greeting" => "hello",
            "planet" => "world"
        );
        $expected = "hello world";
        $actual = TTextTemplate::Merge($tokens,$template);
        $this->assertEquals($expected,$actual);
        $tokens['greeting'] = 'Goodbye';
        $tokens['planet'] = 'Mars';
        $expected = 'Goodbye Mars';
        $actual = TTextTemplate::Merge($tokens,$template);
        $this->assertEquals($expected,$actual);

    }
}
