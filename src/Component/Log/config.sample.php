<?php
/**
 * 日志组件配置文件样例
 */

return [
    //默认驱动
    'default' => 'mongodb',
    //是否开启debug
    'debug'   => false,
    //是否开启Buffer
    'buffer'  => true,
    //所有驱动
    'drivers' => [
        //单一文件驱动
        'single' => [
            'driver'  => 'single',
            'path'    => storage_path('logs'),
            'level'   => 'debug',
            'format'  => 'line'
        ],
        //增加日期文件夹并且分模块拆分文件
        'daily' => [
            'driver'  => 'daily',
            'path'    => storage_path('logs'),
            'level'   => 'debug',
            'format'  => 'line'
        ],
        //日志文件接入kafka
        'Kafka' => [],
        //日志文件直接写入mongodb
        'mongodb' => [
            'driver'  => 'mongodb',
            'path'    => 'root:123456@lnmp-mongodb:27017',
            'database' => 'scs_log',
            'level'   => 'debug',
            'format'  => 'mongo'
        ]
    ],
];


/*
|----------------------------------------------------------------
| ###### driver 日志写入方式 [single, daily, Kafka, mongodb] Default=>single
|----------------------------------------------------------------
| 目前支持文件写入single、daily、mongodb两种
| 1、single 所有日志默认写入配置路径下的 application.log 文件，线上环境运行稳定后，如需日志收集 需启用此模式写入文件
| 2、daily 按天拆分文件夹方式 每天生成一个文件夹。 默认只写入 application.log文件，并支持根据模块拆分文件存储 推荐非线上环境使用，或者暂未启用日志收集的项目使用
| 3、mongodb 日志数据实时写入mongodb
| ---------------------------------------------------------------
| ###### project 接入项目标识 [] Default=>null
| ---------------------------------------------------------------
| 一般是项目的GitLab仓库名字，主要作用有两个
| 1、调用链路追踪上下文定位。
| 2、用于PHP  debug_backtrace中file 路径的切割
| ---------------------------------------------------------------
| ###### path 日志写入路径 [/data/log/php/wwwroot/] Default=>null
| ---------------------------------------------------------------
| 日志写入的路径，确保目录可写。
| 线下环境想要追踪链路调用日志可以调用链上的所有项目配置同一个路径
| ---------------------------------------------------------------
| ###### level 最小日志级别 [debug、info] Default=>debug
| ---------------------------------------------------------------
| 控制日志的写入级别,低于配置级别的日志将不予以记录，级别遵循PSR-3 日志规范中定义的8种日志级别
| debug < info < notice < warning < error < critical < alert < emergency
| ---------------------------------------------------------------
| ###### debug 是否开启debug模式 [true、false] default=>false
| ---------------------------------------------------------------
| 是否开启debug支持，debug模式设置的level自动未debug
| 文件写入只定为单文件模式
| ---------------------------------------------------------------
| ###### buffer 是否开启buffer支持 [true、false] default=>true
| ---------------------------------------------------------------
| 是否开启Buffer支持 开启后日志写入将在进程结束或者有异常退出时才写入硬盘
| ---------------------------------------------------------------
| ###### format 日志记录格式 [json、line] Default=>json
| ---------------------------------------------------------------
| 最终日志写入文件的格式
| 1、json 每行数据将以Json 字符串的形式记录在文件中
| 2、line 正常格式记录文件
| ---------------------------------------------------------------
*/