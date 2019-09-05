<?php

$path = 'https://localhsot/demo/foo/img.jpg';
echo uniqid() . '.' .  pathinfo(basename($path), PATHINFO_EXTENSION);
