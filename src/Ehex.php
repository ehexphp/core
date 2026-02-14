<?php /** @noinspection ALL */

// Session and cookie lifetime (preserved from original)
@ini_set("session.gc_maxlifetime", 360000); // 100 hours
@ini_set("session.cookie_lifetime", 360000);
@session_start();

// Load split class definitions from parts (order preserved)
$__parts = [
    '01-result-page.php',
    '02-html-server-validation.php',
    '03-string-array-object.php',
    '04-convert-console-file.php',
    '05-framework-db-form-date.php',
    '06-http-url-number.php',
    '07-prefs-session-popup-picture.php',
];

$__base = __DIR__ . DIRECTORY_SEPARATOR . 'Ehex' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR;
foreach ($__parts as $__file) {
    require $__base . $__file;
}
unset($__parts, $__base, $__file);
