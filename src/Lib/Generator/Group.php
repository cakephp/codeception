<?php
namespace Cake\Codeception\Lib\Generator;

use Codeception\Util\Template;

class Group extends \Codeception\Lib\Generator\Group
{
    protected $template = <<<EOF
<?php
namespace App\Test\Group;

use Codeception\Event\TestEvent;
use Codeception\Platform\Group;

/**
 * Group class is Codeception Extension which is allowed to handle to all internal events.
 * This class itself can be used to listen events for test execution of one particular group.
 * It may be especially useful to create fixtures data, prepare server, etc.
 *
 * INSTALLATION:
 *
 * To use this group extension, include it to "extensions" option of global Codeception config.
 */
class {{class}}Group extends Group
{
    public static \$group = '{{name}}';

    public function _before(TestEvent \$e)
    {
    }

    public function _after(TestEvent \$e)
    {
    }
}
EOF;
}
