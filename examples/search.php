<?php

require '../classes/class.dzapi.php';

$api = new dzapi();


echo "Search";
print_r($api->search('madonna', 'artist'));

?>