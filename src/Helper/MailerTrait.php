<?php
namespace Cake\Codeception\Helper;

use Cake\Log\Log;

trait MailerTrait
{

    /**
     * Checks that sent email(s) contain `$content`.
     *
     * This requires the email profile(s) used to have `log` equals to
     * `['level' => 'info', 'scope' => 'email']` and a new `email` logging
     * configuration defined as follow:
     *
     * ```
     * 'email' => [
     *     'className' => 'Cake\Log\Engine\FileLog',
     *     'path' => LOGS,
     *     'file' => 'email',
     *     'levels' => ['info'],
     *     'scope' => ['email'],
     * ],
     * ```
     *
     * Finally, it requires the `tests/bootstrap.php` to have:
     *
     * ```
     * use Cake\Log\Log;
     *
     * $logTestConfig = ['path' => TMP . 'tests' . DS] + Log::config('email');
     * Log::drop('email');
     * Log::config('email', $logTestConfig);
     * ```
     *
     * @param array|string $content Content to check for.
     * @return void
     */
    public function seeSentEmailContains($content)
    {
        $logConfig = Log::config('email');
        $path = $logConfig['path'] . $logConfig['file'] . '.log';

        if (!file_exists($path) || !$log = file_get_contents($path)) {
            $this->fail('No email set, cannot assert content.');
        }

        $contents = (array)$content;

        $this->debugSection('email.log', $log);
        foreach ($contents as $content) {
            $this->assertContains($content, $log);
        }

        unlink($path);
    }
}
