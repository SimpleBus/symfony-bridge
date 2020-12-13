<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

foreach ([__DIR__.'/../vendor/autoload.php', __DIR__.'/../../../vendor/autoload.php'] as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

AnnotationRegistry::registerLoader('class_exists');
