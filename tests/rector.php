<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddCoversClassAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DataProviderAnnotationToAttributeRector;
use Rector\PHPUnit\PHPUnit60\Rector\ClassMethod\AddDoesNotPerformAssertionToNonAssertingTestRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DependsAnnotationWithValueToAttributeRector;
use \Rector\Php80\Rector\Class_\AnnotationToAttributeRector;

return static function(RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AddCoversClassAttributeRector::class);
    $rectorConfig->rule(DataProviderAnnotationToAttributeRector::class);
    $rectorConfig->rule(AddDoesNotPerformAssertionToNonAssertingTestRector::class);
    $rectorConfig->rule(DependsAnnotationWithValueToAttributeRector::class);
};
