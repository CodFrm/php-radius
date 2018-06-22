<?php


return [
    /**
     * 调试模式 true或者false
     */
    'debug' => true,

    /**
     * 应用路径,默认值 app/
     */
    'application' => 'app/',

    /**
     * 初始启动组件
     */
    'component' => [
    ],

    /**
     * 绑定抽象类型
     */
    'abstract' => [
        /**
         * 将核心的路由组件替换成自定义的组件
         */
        \HuanL\Core\Components\RouteComponents::class => \App\RouteComponents::class,

        /**
         * 自定义数据库类型
         */
        'dbconnect' => \HuanL\Db\Driver\MySQL\MySQLDBConnect::class,
        'db' => \HuanL\Db\SQLDb::class
    ]
];