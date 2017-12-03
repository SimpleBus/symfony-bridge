<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

require __DIR__ . '/../vendor/autoload.php';

AnnotationRegistry::registerLoader('class_exists');

// Polyfill PHPUnit 6.0 both ways
if (!class_exists('\PHPUnit\Framework\TestCase', true)) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
} elseif (!class_exists('\PHPUnit_Framework_TestCase', true)) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}
