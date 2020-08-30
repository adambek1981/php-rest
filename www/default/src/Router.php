<?php

namespace App;

use App\Controllers\ControllerInterface;
use App\Controllers\NotFound as ControllerNotFound;
use App\Service\Request;

abstract class Router
{
    /** @var array $controllers */
    private static $controllers = [];

    /**
     * @param string $url
     * @param callable $closure
     */
    public static function set(string $url, callable $closure)
    {
        $url = Request::NormalizeUri($url);
        self::$controllers[$url] = $closure;
    }

    /**
     * @throws \LogicException
     */
    public static function run()
    {
        $url = Request::getInstance()->getUrl();

        if (!isset(self::$controllers[$url])) {
            $controller = new ControllerNotFound();
        } else {
            $closure = self::$controllers[$url];

            /** @var ControllerInterface $controller */
            $controller = $closure();
        }

        $controller
            ->process()
            ->sendToStream();
        exit();
    }
}
