<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

require __DIR__ . '/../vendor/autoload.php';

AnnotationRegistry::registerLoader('class_exists');
