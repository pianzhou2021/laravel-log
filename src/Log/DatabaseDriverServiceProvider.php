<?php
/*
 * @Description:
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-01-01 18:10:52
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-01-08 18:04:37
 */
namespace Pianzhou\Laravel\Log;

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
            $connection = $config['connection'] ? app('db')->connection($config['connection']) : app('db.connection');
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
