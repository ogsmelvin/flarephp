<?php

/**
 * 
 * @var array
 */
$database = array(

    /**
     * Default selected database
     */
    'default' => array(
        'host'      => 'localhost',
        'username'  => 'technoc9_dev',
        'password'  => 't3chn0c9_d3v',
        'dbname'    => 'technoc9_sample',
        'driver'    => 'mysql',
        'options'   => array(
            PDO::ATTR_PERSISTENT => true
        )
    )
);

/**
 * 
 * @return array
 */
return $database;