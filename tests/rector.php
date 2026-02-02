<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DataProviderAnnotationToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DependsAnnotationWithValueToAttributeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddCoversClassAttributeRector;
use Rector\PHPUnit\PHPUnit60\Rector\ClassMethod\AddDoesNotPerformAssertionToNonAssertingTestRector;

return static function(RectorConfig $rectorConfig): void {
    if (class_exists(AddCoversClassAttributeRector::class)) {
        $rectorConfig->rule(AddCoversClassAttributeRector::class);
    }
    $rectorConfig->rule(DataProviderAnnotationToAttributeRector::class);
    $rectorConfig->rule(AddDoesNotPerformAssertionToNonAssertingTestRector::class);
    $rectorConfig->rule(DependsAnnotationWithValueToAttributeRector::class);
};
