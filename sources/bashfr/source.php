<?php
/* Fortune Picture
 * v0.2
 * Generates an image from a random BashFr quote.
 * Copyright © Blaiz
 */
 
$name = '[FR] BashFr';

$fortune_data = explode("\n%\n", file_get_contents('sources/'.$this->source.'/bashfr_fortunes')); // Separate all the quotes
$fortune = $fortune_data[rand(0, count($fortune_data) - 1)]; // Choose a random quote
$fortune = preg_replace('/\n\-\-\ http:\/\/www\.bashfr\.org\/\?[0-9]+$/', '', $fortune);

?>
