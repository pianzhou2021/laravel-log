<?php
/*
 * @Description: 
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-03-11 20:44:37
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-03-11 20:49:26
 */

namespace Pianzhou\Laravel\Monolog;

use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('log', function ($app) {
            return new LogManager($app);
        });
    }
}
