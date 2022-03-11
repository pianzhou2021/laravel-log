<?php
/*
 * @Description:
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-01-01 18:10:52
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-03-11 20:49:04
 */
namespace Pianzhou\Laravel\Monolog;

use Illuminate\Support\ServiceProvider;
use Pianzhou\Monolog\Handler\DatabaseHandler;

class DatabaseDriverServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        app('log')->extend('database', function($app, $config){
            $connection = app('db')->connection($config['connection'] ?? '');
            return new \Monolog\Logger($this->parseChannel($config), [
                $this->prepareHandler(
                    new DatabaseHandler(
                        $connection,
                        $config['table'] ?? 'logs',
                        $this->level($config),
                        $config['bubble'] ?? true
                    ), $config
                ),
            ]);
        });

    }
}
