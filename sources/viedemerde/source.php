<?php
/* Fortune Picture
 * v0.2
 * Generates an image from a random VieDeMerde quote.
 * Copyright © Blaiz
 */

$name = "[FR] VieDeMerde";

$fortune_data = simplexml_load_file('http://api.viedemerde.fr/1.0/view/random') or die("Cannot receive data"); // Retrieving data
$fortune = $fortune_data->vdms->vdm->texte; // Getting the text of the VDM

?>
