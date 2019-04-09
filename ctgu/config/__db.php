<?php
return [
    'mysql'=>[
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=zhyd',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',

        // Schema cache options (生产环境须配置以下参数)
        //'enableSchemaCache' => true,
        //'schemaCacheDuration' => 60,
        //'schemaCache' => 'cache',
    ],
    // 阿里云mongodb
    'mongodb'=>[
        'class' => '\yii\mongodb\Connection',
        'dsn' => 'mongodb://@dds-wz9ccf49bd97ae341731-pub.mongodb.rds.aliyuncs.com:3717/admin',
        'options' => [
            "username" => "root",
            "password" => "HJDL2018mongodb"
        ]
    ],
    // 本机
    'redis' => [
        'class' => 'yii\redis\Connection',
        'hostname' => '192.168.0.229',
        'password' => 'tang9902',
        'port' => 6379,
        'database' => 0,
    ],
    // 华为云mongodb
//    'mongodb'=>[
//        'class' => '\yii\mongodb\Connection',
//        'dsn' => 'mongodb://@139.159.254.9:8635/admin',
//        'options' => [
//            "username" => "rwuser",
//            "password" => "HJdl!123"
//        ]
//    ]
];

