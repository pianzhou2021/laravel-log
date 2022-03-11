<?php
/*
 * @Description: 响应内容记录器
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-01-08 22:26:13
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-03-11 20:50:06
 */

namespace Pianzhou\Laravel\Monolog\Middleware;

use Closure;
use Illuminate\Http\Request;

class ResponseLogMiddleware
{
    /**
     * 日志通道
     *
     * @var string
     */
    protected $channel;

    /**
     * 调试模式是否跳过记录
     *
     * @var boolean
     */
    protected $skipIfDebug  = false;

    /**
     * 配置中的路径将不会被记录
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
    
    /**
     * 将响应发送到浏览器后处理任务
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        //如果配置调试模式不记录
        if ($this->skipIfDebug && env('APP_DEBUG')) {
            return;
        }

        //如果存在于排除列表
        if ($this->inExceptArray($request)) {
            return;
        }

        $logger     = ($this->channel) ? app('log')->channel($this->channel) : app('log');
        $content    = $response->content();
        $context    = [
            'version'   => $response->getProtocolVersion(),
            'statusCode'=> $response->getStatusCode(),
            'statusText'=> $response->statusText(),
            'headers'   => $response->headers->all(),
        ];
        
        $logger->log(
            $response->isSuccessful() ? 'INFO' : 'ERROR',
            $content,
            $context
        );
    }


    /**
     * Determine if the request has a URI that should be accessible in maintenance mode.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
