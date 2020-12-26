<?php
namespace gapi\command;


class Test
{

    public static function execute($params, $output)
    {
        echo "============Tester测试用例===========\n";
        $type = $params[0];
        # 获取测试文件
//        $path = ROOT_PATH . 'test';
//        $lists = glob("{$path}/*.php");
//
//        $start_time = microtime(true);
//
//        if($class_name!=null){
//            require $path.'/'.$class_name.'.php';
//            if($func_name!=null){
//                $class_name= '\\test\\'.$class_name;
//                $method_name = $func_name;
//                $this->runFunc($output,$class_name,$method_name);
//            }else{
//                $class = '\\test\\'.$class_name;
//                $this->runFile($output,$class);
//            }
//
//        }else{
//
//            if (!empty($lists)) {
//                foreach ($lists as $file) {
//                    require $file;
//                    $class_name = explode('.',basename($file))[0];
//                    $class = '\\test\\'.$class_name;
//                    $this->runFile($output,$class);
//                }
//            }
//
//        }
//
//
//
//
//        $end_time = microtime(true);
//        $use_time = sprintf("%.3f",$end_time-$start_time);
//        $output->writeln("共执行{$this->i}个测试文件，{$this->j}个方法，用时 {$use_time} 秒");


    }

    protected function runFile($class){
        $class = new \ReflectionClass($class);
        $this->i++;
        foreach ($class->getMethods() as $method) {
            $class_name = $method->class;
            $method_name = $method->name;
            $this->runFunc($class_name,$method_name);
        }
    }

    protected function runFunc($class_name,$method_name){
        $stime = microtime(true);
        $class_name::$method_name();
        $etime = microtime(true);
        echo '\\'.$class_name . '::' . $method_name . '()[' . (sprintf("%.3f", $etime - $stime)) . '] is ok.'."\n";
        $this->j++;
    }
}