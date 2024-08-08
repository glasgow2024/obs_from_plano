<?php

/**
 * OBS from Planorama
 *
 * PHP version 8
 *
 * LICENSE: This source is released under the MIT license.
 *
 * @category  Interface
 * @package   ObsFromPlano
 * @author    James Shields <james@lostcarpark.com>
 * @copyright 2024 James Shields
 * @license   https://opensource.org/license/mit MIT license
 */

require_once 'settings.php';
require_once 'Program.php';
require_once 'People.php';

$people = new People(PEOPLE_FILE);
$program = new Program(PROG_FILE, $people);
$timeNow = new DateTime();

$title = TITLE;
$index = <<<EOD
<html>
  <head>
    <title>$title</title>
  </head>
  <body>
    <h1>$title</h1>
    <ul>
EOD;

foreach ($program->getLocations() as $fileName => $roomName) {
    $fileName = str_replace('/', '_', $fileName);
    $program->writeObsRoomFile(PATH . "$fileName" . EXT, $roomName, $timeNow);
    $index .= '<li><a href="' . $fileName . EXT . '">' . $roomName . '</a></li>';
}

$index .= <<<EOD
    </li>
  </body>
</html>
EOD;

file_put_contents(PATH . '/index.html', $index);
