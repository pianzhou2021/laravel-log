众所周知，Laravel 默认的Logging Channel配置，不支持批量刷新和processors配置
那么我们这个包将针对这个两个功能提供了支持。

使用方法：
直接在app.php中，添加provider配置项(最好靠前配置)
```
Pianzhou\Laravel\Log\LogServiceProvider::class,
```
当配置provider之后，包将会替换系统自带的Log为我们扩展的类
那么这个时候，我们的logging.php将支持如下配置：
```
'single' => [
    'driver' => 'single',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    //是否批量刷新配置
    'batch' => true,
    //processors配置，支持例子中的这两种写法
    'processors'    => [
        Monolog\Processor\UidProcessor::class,
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
    ],
],
```
这需要简单配置provider就可以支持上面的配置，是不是特别方便呢？喜欢麻烦给予点赞加关注
另外，批量操作基于bufferHandler，出了batch来开启的配置，还支持如下配置项：
```
bufferLimit : 当记录达到多少条日志的时候，刷新到介质上。默认为0代表所有日志一次性刷新
flushOnOverflow：当bufferLimit不为0，并且flushOnOverflow设置为false的话，当日志数量达到bufferLimit配置后，将会丢弃最早的日志记录
```