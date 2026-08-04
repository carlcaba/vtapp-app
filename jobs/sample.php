<?php
// Set the path for the log file (ensure the directory is writable)
$logFile = __DIR__ . '/cron_log.txt';

require_once("/home2/logicaadmin/ubioapp.logicaestudio.com/core/classes/user_notification.php");

// Get current timestamp and execution environment info
$currentTime = date('Y-m-d H:i:s');
$executionMode = PHP_SAPI; // Should be 'cli' if run by cron
$test = explode(DIRECTORY_SEPARATOR,__DIR__);$last = array_pop($test);array_push($test,"core","classes");$defdir = implode(DIRECTORY_SEPARATOR,$test);
// Format the message
$message = "[$currentTime] Cron job executed successfully. Mode: $executionMode" . PHP_EOL . $defdir . PHP_EOL;

// Append to the log file
if (file_put_contents($logFile, $message, FILE_APPEND)) {
    echo "Log entry added: $message";
} else {
    echo "Error: Could not write to $logFile";
}
?>