<?php

namespace Hxc\Pay\Command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class Init extends Command
{
    protected function configure()
    {
        $this->setName('InitPay')->setDescription(mb_convert_encoding('初始化支付模块', 'GBK'));
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("---------------------------------------");
        $output->writeln("Init pay.");
        $output->writeln("---------------------------------------");
        $targetPath = APP_PATH . 'extra/';
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }
        if (file_exists($targetPath . 'pay.php')) {
            //配置文件已存在
            $file = realpath(__DIR__ . '/../config.php');
            $output->warning("The configuration file (" . realpath($targetPath . 'pay.php') . ") already exists. Please check {$file} to see if there are any updates.");
        } else {
            copy(__DIR__ . '/../config.php', $targetPath . 'pay.php');
            $output->writeln("Copy config file success");
        }
        $output->writeln("---------------------------------------");
        $targetPath = APP_PATH . 'app/controller/';
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }
        if (file_exists($targetPath . 'Qtpay.php')) {
            //配置文件已存在
            $output->warning("The controller file already exists.");
        } else {
            copy(__DIR__ . '/../Qtpay.php', $targetPath . 'Qtpay.php');
            $output->writeln("Copy controller file success");
        }
    }
}