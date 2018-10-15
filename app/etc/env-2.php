<?php
return [
    'cache_types' => [
        'compiled_config' => 1,
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'target_rule' => 1,
        'full_page' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'translate' => 1,
        'config_webservice' => 1
    ],
    'backend' => [
        'frontName' => 'admin'
    ],
    'queue' => [
        'amqp' => [
            'host' => '',
            'port' => '',
            'user' => '',
            'password' => '',
            'virtualhost' => '/',
            'ssl' => ''
        ]
    ],
    'db' => [
        'connection' => [
            'indexer' => [
                'host' => 'localhost',
                'dbname' => 'm2ecc_db',
                'username' => 'root',
                'password' => 'click123',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1',
                'persistent' => NULL
            ],
            'default' => [
                'host' => 'database.internal',
                'dbname' => 'main',
                'username' => 'user',
                'password' => '',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1'
            ]
        ],
        'table_prefix' => ''
    ],
    'crypt' => [
        'key' => 'a8c10e65fa5c0204c900dfe9371f73ab'
    ],
    'session' => [
        'save' => 'redis',
        'redis' => [
            'host' => 'redis.internal',
            'port' => 6379,
            'database' => '0'
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'production',
    'install' => [
        'date' => 'Fri, 17 Mar 2017 06:34:50 +0000'
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => [
                    'server' => 'redis.internal',
                    'port' => 6379,
                    'database' => '1'
                ]
            ],
            'page_cache' => [
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => [
                    'server' => 'redis.internal',
                    'port' => 6379,
                    'database' => '1'
                ]
            ]
        ]
    ]
];
