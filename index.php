<?php 
	if (isset($_GET["width"]) && isset($_GET["height"]) || isset($_GET["font"]))
	{
		$url;
		
		if (isset($_GET["width"]) && isset($_GET["height"]))
		{
			$url .= addslashes(intval($_GET["width"]))
					 . '/' . 
					addslashes(intval($_GET["height"]))
					 . '/';
		}
		
		if (isset($_GET["font"]))
		{
			$url .= addslashes(intval($_GET["font"])) . '/';
		}
		
		$url .= addslashes(intval($_GET["source"]));
		
		header('Location: ' . $url);
	}
	else
	{
		echo <<<HEADER
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Fortune Picture Generator</title>
	</head>
	<body>
		<div>
HEADER;
		
		echo '
			<form action="'.$_SERVER["PHP_SELF"].'" method="get">
				<label for=""></label>
			</form>
		';
		
		echo <<<FOOTER
		</div>
	</body>
</html>
FOOTER;
	}
?>