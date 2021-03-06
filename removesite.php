<?php 
/*
Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

require_once("ui.inc");
require_once("urls.inc");

$gTitle = "Remove Your Site";
$gRurl = getParam("rurl", "");
?>
<!doctype html>
<html>
<head>
<title><?php echo $gTitle ?></title>
<meta charset="UTF-8">

<?php echo headfirst() ?>
<link type="text/css" rel="stylesheet" href="style.css" />
</head>

<body>
<?php echo uiHeader($gTitle); ?>

<h1><?php echo $gTitle ?></h1>

<?php
$is_valid_url = false;
$url_to_fetch = "";
$bRemovalConfirmed = false;
if ( $gRurl ) {
	// Do some basic validation
	$is_valid_url = preg_match("/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", $gRurl);
  
	if ( ! $is_valid_url ) {
		echo "<p class=warning>The URL entered is invalid: $gRurl</p>\n";
	}
	else {
		$existingUrl = urlExists($gRurl);
		if ( ! $existingUrl ) {
			echo "<p class=warning>Nothing to remove - the URL \"$gRurl\" doesn't exist in the HTTP Archive.</p>\n";
		}
		else {
			// make sure we have a trailing slash
			$url_to_fetch = substr($gRurl, -1) == '/' ? $gRurl : $gRurl . '/';
			$url_to_fetch .= 'removehttparchive.txt';
			// This requires setting this in php.ini: allow_url_fopen = On
			$bRemovalConfirmed = ( FALSE === @fetchUrl($url_to_fetch) ? false : true );
			if ( ! $bRemovalConfirmed ) {
				echo "<p class=warning><a href='$url_to_fetch' style='text-decoration: underline; color: #870E00;'>$url_to_fetch</a> was not found.<br>$gRurl is still archived.</p>\n";
			}
			else {
				removeSite($gRurl);  // queue it for removal
				echo "<p class=warning style='margin-bottom: 0;'>$gRurl will be removed within five business days.</p>\n<p style='margin-top: 0;'>You can remove removehttparchive.txt now.</p>";
			}
		}
	}
}
?>

<script type="text/javascript">
function confirmRemove() {
	var url = document.getElementById("rurl").value;
	if ( ! url ) {
		alert("Please enter a URL.");
	}
	else if ( confirm("This will remove ALL DATA about " + url + " and remove it from all future archiving. Do you want to continue?") ) {
		return true;
	}

	return false;
}
</script>

<p>
Follow these steps to remove your website's data from the HTTP Archive and prevent further archiving.
</p>

<ol style="margin-left: 2em;">
<form name=removesite action="<? echo $_SERVER['PHP_SELF'] ;?>" onsubmit="return confirmRemove()">
  <li> Enter your URL:
  <span class="ui-widget" style="font-size: 1em;">
    <input id="rurl" name="rurl" style="margin: 0;" size=35 />
  </span>

  <li> Create a file called <code>removehttparchive.txt</code> at that URL.

  <li> Click here: <input type="submit" value="Remove" name="submit" placeholder="http://www.example.com" style="margin: 0;" />
</form>
</ol>



<?php echo uiFooter() ?>

</body>
</html>
