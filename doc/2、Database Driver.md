让我们的Log系统支持Database Driver配置

开始之前，我们需要先增加一下数据表，以MySQL为例：
```
CREATE TABLE IF NOT EXISTS `t_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `channel` varchar(20) NOT NULL,
  `level_name` varchar(20) NOT NULL,
  `level` smallint(6) NOT NULL,
  `context` text NOT NULL,
  `extra` text NOT NULL,
  `datetime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;
```
然后直接在app.php中，添加provider配置项
```
Pianzhou\Laravel\Log\DatabaseDriverServiceProvider::class,
```
当配置provider之后，我们的logging.php将支持如下配置：
```
'database' => [
    'driver'        => 'database',
    'level'         => env('LOG_LEVEL', 'debug'),
    'batch'         => true,
    // 'connection'    => 'mysql',
    // 'table'         => 'logs',
    'formatter'     => Pianzhou\Monolog\Formatter\TransformScalarFormatter::class,
],
```
配置完成，就是这么简单

备注：Database Driver基于pianzhou/monolog,需要配置Pianzhou\Monolog\Formatter\TransformScalarFormatter格式化一起使用。