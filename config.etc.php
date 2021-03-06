<?php
/**
 * Sample configuration
 */

$gConsumerKey = ''; // Your Consumer token
$gConsumerSecret = ''; // Your Consumer secret
$gAccessKey = ''; // The token that used to access your Consumer
$gAccessSecret = ''; // The secret that used to access your Consumer

$gEntryPoint = ''; // Which wiki do you want to improve? (E.g. zhwiki, enwiki)

$gFixType = ''; // Specify Hclear-bot to fix the errors of the given error type

$gMaxLag = ''; // An integer number of seconds that specify edits maximum lag value (Default value: 5)

$gEditLimit = ''; // An number of seconds that specify how much time the next edit should elapse from the previous edit (Default value: 5)

$gMaxJob = ''; // An integer number that specify job execute times (Default value: -1)

$gFixerMaxQuery = ''; // An integer number that each working maximum query lint API (Default value: 20)

$gAllowFixNamespace = ''; // An integer number or an array that Hclear-bot only fix there namespaces

$gEditMsg = ''; // The edit message, (Default value: 'Fix multiple-unclosed-formatting-tags error')

$gIsSemiFix = true; // Whether to enable semi-automatic fix (Default value: true)