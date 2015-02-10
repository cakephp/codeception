<?php
namespace Cake\Codeception\Lib\Generator;

use Codeception\Util\Template;

class Helper extends \Codeception\Lib\Generator\Helper
{

    protected $template = <<<EOF
<?php
namespace App\TestSuite\Codeception;

use Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in \$I

class {{name}}Helper extends Module
{

}

EOF;
}
