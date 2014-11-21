<?php

// expiration time in seconds
define("MEMCACHED_EXPIRATION_TIME", 15);

global $MEMCACHED_HOST_ARRAY;
$MEMCACHED_HOST_ARRAY = array(
  array('host' => 'localhost', 'port' => 11211 )
);