<?php

require_once 'src/Ehex.php';

echo "Ehex Framework Loaded Successfully\n";

// Test loading some classes
$testClasses = [
    'ResultStatus1',
    'String1',
    'Array1',
    'FileManager1',
    'Url1',
    'Cookie1',
    'Session1',
    'Api1',
    'Controller1',
    'Model1',
    'AuthModel1',
    'HtmlForm1'
];

foreach ($testClasses as $className) {
    if (class_exists($className)) {
        echo "[SUCCESS] Class $className is loaded.\n";
    } else {
        echo "[FAILED] Class $className NOT found!\n";
        // Try to trigger autoloader by instantiating if it's not pre-loaded
        try {
            // Use reflection for abstract classes
            $rc = new ReflectionClass($className);
            echo "[SUCCESS] Class $className found via Reflection.\n";
        } catch (Error $e) {
            echo "[ERROR] Could not load $className: " . $e->getMessage() . "\n";
        } catch (ReflectionException $e) {
            echo "[ERROR] Reflection failed for $className: " . $e->getMessage() . "\n";
        }
    }
}

// Test some functionality
echo "Testing String1::convertToCamelCase('test_string'): " . String1::convertToCamelCase('test_string') . "\n";
echo "Testing Array1::toArray('a,b,c', ','): " . json_encode(Array1::toArray('a,b,c', ',')) . "\n";

echo "Verification Complete.\n";
