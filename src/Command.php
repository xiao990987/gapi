<?php
namespace gapi;
/**
 * Class Console
 * @package GFPHP
 */
class Command
{
    protected string $name = " 
 = = = = = = = = = = = = = = = = = = = = GAPI = = = = = = = = = = = = = = = = = = = = =
 ";
    protected $stdout;
    protected $stdin;
    protected $stderr;
    protected $argv;

    /**
     * Command constructor.
     */
    public function __construct()
    {
        error_reporting(E_ALL ^ E_NOTICE);
        date_default_timezone_set("PRC");
        $this->stdout = fopen('php://stdout', 'wb');
        $this->stdin = fopen('php://stdin', 'rb');
        $this->stderr = fopen('php://stderr', 'wb');

    }
    /**
     * 执行
     */
    public function execute()
    {
        array_shift($_SERVER['argv']);
        $this->argv = $_SERVER['argv'];
        $class = "\\gapi\\command\\{$this->argv[0]}";
        array_shift($this->argv);
        $class::execute($this->argv,$this);
    }


    /**
     * 输出一行
     * @param string $message 输出的消息
     */
    public function writeln($message)
    {
        $this->write($message . "\r\n");
    }

    /**
     * 输出内容
     * @param string $message 输出的消息
     */
    public function write(string $message): void
    {
        if (!is_string($message)) {
            $message = var_export($message, true);
        }
        fwrite($this->stdout, $message);
    }

    /**
     * 读取一行内容
     * @param string $notice
     * @return array
     */
    public function getStdin($notice = ''): array
    {
        $this->write($notice);
        return explode(' ', str_replace(["\r\n", "\n"], '', fgets($this->stdin)));
    }

}