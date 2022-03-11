<?php
namespace Pianzhou\Laravel\Log;

use Illuminate\Log\LogManager as LaravelLogManager;
use InvalidArgumentException;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Processor\ProcessorInterface;
use Psr\Log\LoggerInterface;

class LogManager extends LaravelLogManager
{
    /**
     * 重写方法
     * 
     */
    protected function prepareHandler(HandlerInterface $handler, array $config = []) : HandlerInterface
    {
        $handler    = parent::prepareHandler($handler, $config);

        if (isset($config['batch']) && $config['batch']) {
            $handler    = new BufferHandler($handler, $config['bufferLimit'] ?? 0, $this->level($config), $config['bubble'] ?? true, $config['flushOnOverflow'] ?? false);
        }

        return $handler;
    }


    /**
     * Create an instance of the single file log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createSingleDriver(array $config)
    {
        return $this->prepareProcessors(
            parent::createSingleDriver($config)
        , $config);
    }

    /**
     * Create an instance of the daily file log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createDailyDriver(array $config)
    {
        return $this->prepareProcessors(
            parent::createDailyDriver($config)
        , $config);
    }

    /**
     * Create an instance of the Slack log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createSlackDriver(array $config)
    {
        return $this->prepareProcessors(
            parent::createSlackDriver($config)
        , $config);
    }

    /**
     * Create an instance of the syslog log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createSyslogDriver(array $config)
    {
        return $this->prepareProcessors(
            parent::createSyslogDriver($config)
        , $config);
    }

    /**
     * Create an instance of the "error log" log driver.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function createErrorlogDriver(array $config)
    {
        return $this->prepareProcessors(
            parent::createErrorlogDriver($config)
        , $config);
    }

    /**
     * Create an instance of any handler available in Monolog.
     *
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     *
     * @throws \InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createMonologDriver(array $config)
    {
        return $this->prepareProcessors(
            parent::createMonologDriver($config)
        , $config);
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->prepareProcessors(
                $this->customCreators[$config['driver']]($this->app, $config)
            , $config);
    }

    /**
     * 初始化Processors
     * 
     * @param  LoggerInterface  $logger
     * @param  array  $config
     * @return \Psr\Log\LoggerInterface
     */
    protected function prepareProcessors(LoggerInterface $logger, array $config = []) : LoggerInterface
    {
        if (isset($config['processors'])) {
            foreach ($config['processors'] as $key => $value) {
                if (is_object($value)) {
                    $processor  = $value;
                } elseif (is_array($value)) {
                    $processor  = $this->app->make($key, $value);
                } else {
                    $processor  = $this->app->make($value);
                }

                if (! is_a($processor, ProcessorInterface::class, true)) {
                    throw new InvalidArgumentException(
                        $processor.' must be an instance of '.ProcessorInterface::class
                    );
                }

                $logger->pushProcessor($processor);
            }
        }

        return $logger;
    }
}