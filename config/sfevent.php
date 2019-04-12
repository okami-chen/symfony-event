<?php


return [

    'logger' => 'sfevent', //logging channels name

    'listener' => [
        //symfony event listener
    ],

    'subscriber' => [
        //symfony event subscriber
    ],

    'flow' => [
        'name'  => 'state',
        'path'  => base_path('data/workflow')
    ],
];
