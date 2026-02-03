#!/usr/bin/env php
<?php

declare(strict_types=1);


require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Keystone\Cli\Command\MakePluginCommand;
use Keystone\Cli\Command\PluginAddCommand;
use Keystone\Cli\Command\PluginDoctorCommand;


$application = new Application('Keystone CLI', '1.0.0');

$application->add(new MakePluginCommand());
$application->add(new PluginAddCommand());
$application->add(new PluginDoctorCommand());

$application->run();

?>