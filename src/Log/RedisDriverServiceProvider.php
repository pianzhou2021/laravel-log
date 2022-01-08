<?php
/*
 * @Description: 注册redis log driver
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-01-01 18:10:52
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-01-08 18:15:19
 */
namespace Pianzhou\Laravel\Log;

use Illuminate\Support\ServiceProvider;
use Monolog\Handler\RedisHandler;

class RedisDriverServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        app('log')->extend('redis', function($app, $config){
            $connection = $config['connection'] ? app('redis')->connection($config['connection']) : app('redis.connection');
            return new \Monolog\Logger($this->parseChannel($config), [
                $this->prepareHandler(
                    new RedisHandler(
                        $connection,
                        $config['key'] ?? 'logs',
                        $this->level($config),
                        $config['bubble'] ?? true
                    ), $config
                ),
            ]);
        });

    }
}
