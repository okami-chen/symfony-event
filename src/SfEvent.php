<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-4-11 0011
 * Time: 21:24
 */

namespace OkamiChen\SymfonyEvent;


use Illuminate\Support\Facades\Facade;

class SfEvent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Symfony\Component\EventDispatcher\EventDispatcher::class;
    }
}