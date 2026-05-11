<?php

namespace app\openapi\http\middleware;

use app\common\exception\ControllerExtendException;
use app\common\service\JsonService;
use app\openapi\controller\BaseOpenController;
use think\exception\ClassNotFoundException;
use think\exception\HttpException;

class InitMiddleware
{
    public function handle($request, \Closure $next)
    {
        try {
            $controller = str_replace('.', '\\', $request->controller());
            $controller = '\\app\\openapi\\controller\\' . $controller . 'Controller';
            $controllerClass = invoke($controller);
            if (($controllerClass instanceof BaseOpenController) === false) {
                throw new ControllerExtendException($controller, '404');
            }
        } catch (ClassNotFoundException $e) {
            throw new HttpException(404, 'controller not exists:' . $e->getClass());
        }

        $request->controllerObject = $controllerClass;
        return $next($request);
    }
}
