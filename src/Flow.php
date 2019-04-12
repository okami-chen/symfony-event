<?php
/**
 * Created by PhpStorm.
 * Project: bes.131.im
 * Author: DeHua Chen
 * Email: x25125x@126.com
 * Date: 2019-04-12
 * Time: 14:09
 */

namespace OkamiChen\SymfonyEvent;


use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Flow
 * @package OkamiChen\SymfonyEvent
 * @author DeHua Chen
 */
class Flow
{
    private $name;//声明一个私有的实例变量

    private function __construct()
    {

    }

    /**
     * @var Workflow[]
     */
    public static $instance;//声明一个静态变量（保存在类中唯一的一个实例）

    /**
     * @param $name
     * @return Workflow
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function getInstance($name)
    {
        if (!self::$instance[$name]) {
            self::$instance = static::process($name);
        }
        return self::$instance;
    }


    /**
     * @param $node
     * @return Workflow
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected static function process($node)
    {
        $config = app('config')['sfevent']['flow'];

        $path = rtrim($config['path'], '/') . '/' . $node . '.yaml';

        $rows = Yaml::parseFile($path);

        $definitionBuilder = new DefinitionBuilder();

        foreach ($rows['places'] as $key => $row) {
            $definitionBuilder->addPlace($row);
        }

        foreach ($rows['transitions'] as $name => $tran) {
            if (is_array($tran['from'])) {
                foreach ($tran['from'] as $k => $v) {
                    $definitionBuilder->addTransition(new Transition($name, $v, $tran['to'][$k] ?? $tran['to']));
                }
                continue;
            }
            $definitionBuilder->addTransition(new Transition($name, $tran['from'], $tran['to']));
        }

        $definition = $definitionBuilder->build();

        $marking = new SingleStateMarkingStore($config['name']);

        return new Workflow($definition, $marking, app()->make('sfevent'), 'order');
    }
}
