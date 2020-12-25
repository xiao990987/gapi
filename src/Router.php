<?php
namespace gapi;
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION | \Attribute::IS_REPEATABLE)]
class Router{
    protected $handler;

    public function __construct(
        public string $path = '',
        public array $methods = []
    ) {

    }

    public function setHandler($handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    public function run():void
    {
        call_user_func([new $this->handler->class, $this->handler->name]);
    }


}