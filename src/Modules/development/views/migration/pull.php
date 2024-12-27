<?php
set_time_limit(120);

// Forked from https://gist.github.com/1809044
// Available from https://gist.github.com/nichtich/5290675#file-deploy-php

$TITLE   = 'Git Deployment Hamster';
$VERSION = '0.11';
$mykey   = '1234567890qwertyuiop';

echo <<<EOT
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>$TITLE</title>
</head>
<body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
<pre>
  o-o    $TITLE
 /\\"/\   v$VERSION
(`=*=') 
 ^---^`-.


EOT;

// check secret key
if(!isset($_GET['key'])) {
     echo "<span style=\"color: #ff0000\">Sorry, secret key required!</span>\n";
    echo "</pre>\n</body>\n</html>";
    exit;
}

if ($_GET['key'] != $mykey) {
     echo "<span style=\"color: #ff0000\">Sorry, your key is not valid!</span>\n";
    echo "</pre>\n</body>\n</html>";
    exit;
}

flush();

// Actually run the update
$commands = array(
    'cd ',
	'echo $PWD',
  'whoami',
  'git config user.name "Toni Haryanto"',
	'git config user.email "toha.samba@gmail.com"',
	'git pull origin master',
	'git status',
	'git submodule sync',
	'git submodule update',
  'git submodule status',
	'php composer.phar update'
);

$output = "\n";

$log = "####### ".date('Y-m-d H:i:s'). " #######\n";

foreach($commands AS $command){
    // Run it
    $tmp = shell_exec("$command 2>&1");
    // Output
    $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
    $output .= htmlentities(trim($tmp)) . "\n";

    $log  .= "\$ $command\n".trim($tmp)."\n";
}

$log .= "\n";

// file_put_contents ('deploy-log.txt',$log,FILE_APPEND);

echo $output; 

?>
</pre>
</body>
</html>