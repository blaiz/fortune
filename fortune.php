<?php
/* Fortune Picture
 * v0.2
 * Generates an image from a random text source, usually a quote.
 * Copyright © Blaiz
 */

class fortunepicture
{
	private $source; // The source to retrieve data from
	private $fortune; // The fortune to draw
	
	private $im; // The image
	
	private $width = 500; // Width of the image, 500 pixels is default value
	private $height = 100; // Height of the image, 100 pixels is default value
	
	private $positionx; // Current horizontal position
	private $lineheight; // Position from the top
	private $positiony; // Current vertical position
	private $interline = 6; // Space between the lines
	private $maxcharsonaline; // Maximum number of characters on one line
	private $maxlines; // Maximum number of text lines that can be drawn
	
	private $text_color; // Color of the text
	
	private $font = 1; // ID of the chosen font, 1 is default value
	private $fontsize; // Size of the font
	
	function __construct()
	{
		// Used to redirect the user to the fortune generator when the URL is mistaped
		if (isset($_GET["error"]))
		{
			header("Location: /");
			exit(0);
		}
		
		header("Content-type: image/png");
		
		if (isset($_GET["width"]))
		{
			$this->width = intval(addslashes($_GET["width"]));
			if ($this->width < 100)
			{
				$location  = preg_replace('#\/'.$this->width.'\/#', '/100/', 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], 1);
				header('Location: '.$location);
				exit(0);
			}
			else if ($this->width > 1000)
			{
				$location  = preg_replace('#\/'.$this->width.'\/#', '/1000/', 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], 1);
				header('Location: '.$location);
				exit(0);
			}
		}
		
		if (isset($_GET["height"]))
		{
			$this->height = intval(addslashes($_GET["height"]));
			if ($this->height < 100)
			{
				$location  = preg_replace('#\/'.$this->height.'\/#', '/100/', 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], 1);
				header('Location: '.$location);
				exit(0);
			}
			else if ($this->height > 1000)
			{
				$location  = preg_replace('#\/'.$this->height.'\/#', '/1000/', 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'], 1);
				header('Location: '.$location);
				exit(0);
			}
		}
		
		if (isset($_GET["font"]))
			$this->font = intval(addslashes($_GET["font"]));
		
		$this->im = @imagecreatetruecolor($this->width, $this->height) or die("Cannot Initialize new GD image stream"); // Try to create a new image
		
		// This trick (found here: http://www.phpgd.com/scripts.php?script=27) permits to have a true transparent background and transparent antialiasing
		$transparentimage = imagecreatefromstring (base64_decode ('
			iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAK/
			INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAA
			DqSURBVHjaYvz//z/DYAYAAcTEMMgBQAANegcCBNCgdyBAAA16BwIE0KB3I
			EAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcg
			QAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyB
			AAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IE
			AADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQ
			AANegcCBNCgdyBAgAEAMpcDTTQWJVEAAAAASUVORK5CYII='));
		imagealphablending($this->im, false);
        imagesavealpha($this->im, true);
        imagecopyresized($this->im, $transparentimage, 0, 0, 0, 0, $this->width, $this->height, imagesx($transparentimage), imagesy($transparentimage));
        
        if (isset($_GET["color"]))
		{
			$color = sscanf(addslashes($_GET["color"]), '%2x%2x%2x');
			$this->text_color = @imagecolorallocate($this->im, $color[0], $color[1], $color[2]);
		}
		else
			$this->text_color = @imagecolorallocate($this->im, 0, 0, 0); // Default text color is black
		
		$this->font = glob("fonts/".$this->font."*.ttf");
		$this->font = $this->font[0];
		preg_match('#([0-9.]+)\-([0-9.]+)\.ttf$#', $this->font, $this->fontsize);
		
		$this->positionx = $this->fontsize[1];
		$this->lineheight = $this->positiony = $this->fontsize[2] + $this->interline; // Add some marging to place the text correctly
		
		$this->maxcharsonaline = floor(($this->width - $this->positionx * 2) / $this->fontsize[1]);
		$this->maxlines = round(($this->height - $this->interline * 2) / $this->lineheight);
		
		if (isset($_GET["source"]))
		{
			$this->source = addslashes($_GET["source"]); // Getting the source from the URL
			if (file_exists('sources/'.$this->source.'/source.php'))
			{	
				do
				{
					$this->fortune = $this->getfortune();
				}
				while (!$this->drawline($this->fortune));
			}
			else
			{
				header("Location: /");
				exit(0);
			}
		}
		else
		{
			header("Location: /");
			exit(0);
		}
		
		imagepng($this->im);
		@imagedestroy($this->im);
	}
	
	private function getfortune()
	{
		include('sources/'.$this->source.'/source.php');
		
		return $fortune;
	}
	
	/**
	* Draws lines of text from a fortune source
	*
	* @param $fortune A fortune
	* @return bool
	*/
	private function drawline($fortune)
	{
		$fortune = wordwrap($fortune, $this->maxcharsonaline, "\n", true);
		$fortunelines = explode("\n", $fortune);
		
		if (count($fortunelines) > $this->maxlines)
		{
			return false;
		}
		else
		{
			foreach ($fortunelines as $line)
			{
				@imagettftext($this->im, 
							  $this->fontsize[2], 
							  0, 
							  $this->positionx, 
							  $this->positiony, 
							  $this->text_color, 
							  $this->font, 
							  $line); // Drawing a word
							  
				$this->positiony += $this->lineheight; // Vertical position is set one line under
			}
			
			return true;
		}
	}
}

$image = new fortunepicture();

?>