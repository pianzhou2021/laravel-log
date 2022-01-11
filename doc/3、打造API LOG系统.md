# 今天我们通过我们打造一个api 响应日志功能

## 1、开始之前，我们新建一个数据表，以MySQL为例：
```
CREATE TABLE IF NOT EXISTS `t_response_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `result` text NOT NULL,
  `channel` varchar(20) NOT NULL,
  `level_name` varchar(20) NOT NULL,
  `level` smallint(6) NOT NULL,
  `uid` varchar(32) NOT NULL,
  `process_id` int(11) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `url` varchar(255) NOT NULL,
  `http_method` varchar(10) NOT NULL,
  `query_string` varchar(255) NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `post` text NOT NULL,
  `context` text NOT NULL,
  `extra` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;
```

## 2、新增好数据表后，我们让系统支持processors和Database Driver
在app.php中，添加provider配置项(最好靠前配置)
```
Pianzhou\Laravel\Log\LogServiceProvider::class,
Pianzhou\Laravel\Log\DatabaseDriverServiceProvider::class,
```
## 3、然后，在logging.php配置文件中
```
'response_log' => [
    'driver'        => 'database',
    'level'         => env('LOG_LEVEL', 'debug'),
    'batch'         => true,
    // 'connection'    => 'mysql',
    'table'         => 'response_logs',
    'formatter'     => Pianzhou\Monolog\Formatter\TransformScalarFormatter::class,
    'formatter_with'    => [
        // 'dateFormat'    => 'Y-m-d H:i:s',
        'transfroms'    => [
            'result'        => 'message',
            'channel'       => 'channel',
            'level_name'    => 'level_name',
            'level'         => 'level',
            'context'       => 'context',
            'extra'         => 'extra',
            'created_at'    => 'datetime',
            'process_id'    => 'extra.process_id',
            'uid'           => 'extra.uid',
            'url'           => 'extra.url',
            'ip'            => 'extra.ip',
            'http_method'   => 'extra.http_method',
            'query_string'  => 'extra.query_string',
            'referrer'      => 'extra.referrer',
            'post'          => 'extra.post',
        ]
    ],
    'processors'    => [
        Monolog\Processor\UidProcessor::class,
        Monolog\Processor\ProcessIdProcessor::class,
        Monolog\Processor\WebProcessor::class => [
            'extraFields' => [
                'url'         => 'REQUEST_URI',
                'ip'          => 'REMOTE_ADDR',
                'http_method' => 'REQUEST_METHOD',
                'server'      => 'SERVER_NAME',
                'referrer'    => 'HTTP_REFERER',
                'query_string'=> 'QUERY_STRING',
            ],
        ],
        Pianzhou\Monolog\Processor\PostProcessor::class    => [
            'maskFileds'    => [
                'password',
                'password_confirm',
                'data.password',
                'data.username.a',
            ]
        ]
    ],
],
```
## 4、在app\Http\Middleware目录中，新增ApiLog.php代码如下：
```
<?php
/*
 * @Description: 响应内容记录器
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-01-08 22:26:13
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-01-10 23:14:19
 */

namespace App\Http\Middleware;

use Pianzhou\Laravel\Log\Middleware\ResponseLogMiddleware;

class ApiLog extends ResponseLogMiddleware
{
    /**
     * 日志通道
     *
     * @var string
     */
    protected $channel = 'response_log';

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
}

```

## 5、新增好Middleware后，我们就可以在app\Http\Kernel.php中配置了

### 5.1 如果需要全局记录，则在middleware属性中配置：
```
    protected $middleware = [
        \App\Http\Middleware\ApiLog::class,
    ];
```

### 5.2 如果只是需要在api，或者web中使用，则配置在middlewareGroups属性中：
```
    protected $middlewareGroups = [
        'web' => [
           // \App\Http\Middleware\ApiLog::class,
        ],

        'api' => [
            \App\Http\Middleware\ApiLog::class,
        ],
```
### 5.3 如果，我们想在路由中使用则配置在routeMiddleware属性中：
```
    protected $routeMiddleware = [
        'response.log'=> \App\Http\Middleware\ApiLog::class,
    ];
```
这样，我们就可以在route中使用，例如：
```
Route::middleware('response.log')->any('/', function () {
    return 'test';
}
```