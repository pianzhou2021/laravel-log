# pianzhou/laravel-monolog

## 安装

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

```
composer require pianzhou/laravel-monolog
```

# 数据库Driver
使用方法：
直接在app.php中，添加provider配置项
```
Pianzhou\Laravel\Log\LogServiceProvider::class,
Pianzhou\Laravel\Log\DatabaseDriverServiceProvider::class,
```
logging.php中，添加channel配置项
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