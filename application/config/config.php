<?php

return array(

    /**
     * Define modules
     */
    'modules' => array('main'),

    /**
     * To return it to its default value
     * just give a value NULL
     */
    'timezone' => 'Asia/Manila',

    /**
     * To return it to its default value
     * just give a value NULL
     */
    'time_limit' => null,

    /**
     * To return it to its default value
     * just give a value NULL
     */
    'memory_limit' => null,

    /**
     * Global XSS Filter
     */
    'auto_xss_filter' => false,

    /**
     * GZIP output compression
     */
    'auto_compress' => true,

    /**
     * Default response content type
     */
    'default_content_type' => 'text/html',

    /**
     * Allow override of config in controller
     */
    'allow_override' => true

);
