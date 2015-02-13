<?php
namespace Cake\Codeception\Lib\Generator;

class Cest {

    protected $template = <<<EOF
<?php
{{namespace}}

class {{name}}Cest
{
    // @codingStandardsIgnoreStart
    public function _before(UnitTester $I)// @codingStandardsIgnoreEnd
    {
    }

    // @codingStandardsIgnoreStart
    public function _after(UnitTester $I)// @codingStandardsIgnoreEnd
    {
    }

    // tests
    public function tryToTest({{actor}} \$I)
    {
    }
}
EOF;
}
