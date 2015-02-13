<?php
namespace Cake\Codeception\Lib\Generator;

use Codeception\Util\Template;

class Helper extends \Codeception\Lib\Generator\Helper
{
    /**
     * Template used by `codecept bootstrap` to create the custom
     * modules (helpers) stubs in `src/TestSuite/Codeception`.
     *
     * @var string
     */
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
