<?php

namespace App\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

class HandleExceptions extends \Illuminate\Foundation\Bootstrap\HandleExceptions
{
    public function bootstrap(Application $app)
    {
        parent::bootstrap($app);
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    }

    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (in_array($level, [E_DEPRECATED, E_USER_DEPRECATED], true)) {
            return;
        }

        parent::handleError($level, $message, $file, $line, $context);
    }
}
