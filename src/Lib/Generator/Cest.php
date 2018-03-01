<?php
namespace Cake\Codeception\Lib\Generator;

class Cest extends \Codeception\Lib\Generator\Cest
{

    protected $template = <<<EOF
<?php
{{namespace}}

class {{name}}Cest
{
    // @codingStandardsIgnoreStart
    public function _before({{actor}} \$I)// @codingStandardsIgnoreEnd
    {
    }

    // @codingStandardsIgnoreStart
    public function _after({{actor}} \$I)// @codingStandardsIgnoreEnd
    {
    }

    // tests
    public function tryToTest({{actor}} \$I)
    {
    }
}

EOF;
}
