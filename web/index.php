<?php

require(__DIR__ . '/../vendor/autoload.php');

$config = require(__DIR__ . '/../config/config.php');

(new phantomd\ShopCart\modules\base\Application($config))->run();
