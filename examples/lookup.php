<?php

require '../classes/class.dzapi.php';

$api = new dzapi();

echo "Lookup artist";
print_r($api->lookup('13', 'artist', true));


?>
