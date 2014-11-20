<?php

function curl_it( $url, $showOnlyErrors=0 ) {
	$ch = curl_init(); // create cURL handle (ch)
	if (!$ch) {
	    die("Couldn't initialize a cURL handle");
	}
	// set some cURL options
	$ret = curl_setopt($ch, CURLOPT_URL,            $url);
	$ret = curl_setopt($ch, CURLOPT_HEADER,         1);
	// $ret = curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$ret = curl_setopt($ch, CURLOPT_TIMEOUT,        30);

	// execute
	$ret = curl_exec($ch);

	if (empty($ret)) {
	    die(curl_error($ch));
	    curl_close($ch);
	} else {
	    $info = curl_getinfo($ch);
	    curl_close($ch);
	    if ( empty( $info['http_code'] ) ) {
			echo "No HTTP code was returned";
			echo "<br />";
			return false;
	    } else {
	    	if( $info['http_code'] == '200' ) {
	    		if( ! $showOnlyErrors ) {
	    			echo "<a href='{$url}' target='_blank'>{$url}</a> returned status of {$info['http_code']}";
	    			echo "<br />";
	    		}
	    	} elseif( $info['http_code'] == '301' ) {
	    		if( ! $showOnlyErrors ) {
					echo "<a href='{$url}' target='_blank'>{$url}</a> returned status of {$info['http_code']} and was redirected to <a href='{$info['redirect_url']}' target='_blank'>{$info['redirect_url']}</a>";
	    			echo "<br />";
	    		}
	    	} else {
	    		echo "<strong class='text-danger'><a href='{$url}' target='_blank'>{$url}</a> returned status of {$info['http_code']}</strong>";
	    		echo "<br />";
	    		return false;
	    	}
	    }
	}
	return true;
}



?>
<!doctype html>
<html>
	<head>
		<title>Check URL Status</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
		<style>
		#htaccess-container {
			display:none;
			padding: 1%;
			width: 100%;
		}
		.pad {
			padding: 1%;
		}
		img.pad {
			max-width: 80%;
		}
		</style>
	</head>
	<body>
		<div class="container">
			<h1>RO Status Checker</h1>
			<?

			if( isset( $_POST['urls'] ) ) {
				$urls 		= explode("\r\n", $_POST['urls']);
				$checkbox 	= isset( $_POST['checkbox'] ) ? 1 : 0;
				foreach( $urls as $url ) {
					if( substr($url, 0, 4) !== 'http') {
						$url = 'http://' . $url;
					}
					$checkUrl = curl_it( $url, $checkbox);
					if( ! $checkUrl ) {
						$urlChecker = preg_match_all('/http:\/\/(.*)\.(com|org|net)\/(.*)/', $url, $matches);
						$badUrls[] = $matches[3][0];
					}
				}
				?>
				<hr />
				<? if ( isset( $badUrls ) ) : ?>
					<p>
						Redirect to: <input type="text" name="redirectTo" id="redirectTo" value="/" /> &nbsp; <a id="download" href="#" class="btn btn-sm btn-primary">Get sample .htaccess code</a>
					</p>
				<? else : ?>
					<p><strong>No bad URLs found</strong></p>
				<? endif ?>
				<code id="htaccess-container"></code>
				<hr />
				<h4><a href="/" class="btn btn-default">Start Over</a></h4>
				<?
			} else {
				?>
				<form action="/" method="post">
					<div class="form-group">
						<textarea class="form-control" name="urls" id="url-container" placeholder="Paste in a list of urls to test, one per line" rows="20"></textarea>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="checkbox" id="checkbox" value="1">Only show errors?
						</label>
					</div>
					
					<button type="submit" class="btn btn-primary" />Check These URLs</button>
				</form>
				<hr />
			<h3>Other useful bits</h3>
<pre># Redirect all pages on current site to newsite
Redirect 301 / http://newsite.com/

# Help
http://www.addedbytes.com/for-beginners/url-rewriting-for-beginners/

#-------------------------------------
# Flags cheat sheet
#-------------------------------------
# C (chained with next rule)
# CO=cookie (set specified cookie)
# E=var:value (set environment variable var to value)
# F (forbidden - sends a 403 header to the user)
# G (gone - no longer exists)
# H=handler (set handler)
# L (last - stop processing rules)
# N (next - continue processing rules)
# NC (case insensitive)
# NE (do not escape special URL characters in output)
# NS (ignore this rule if the request is a subrequest)
# P (proxy - i.e., apache should grab the remote content specified in the substitution section and return it)
# PT (pass through - use when processing URLs with additional handlers, e.g., mod_alias)
# R (temporary redirect to new URL)
# R=301 (permanent redirect to new URL)
# QSA (append query string from request to substituted URL)
# S=x (skip next x rules)
# T=mime-type (force specified mime type)

#-------------------------------------
# Regular expression cheat sheet
#-------------------------------------
# . (any character)
# * (zero of more of the preceding)
# + (one or more of the preceding)
# {} (minimum to maximum quantifier)
# ?  (ungreedy modifier)
# ! (at start of string means "negative pattern")
# ^ (start of string, or "negative" if at the start of a range)
# $ (end of string)
# [] (match any of contents)
# - (range if used between square brackets)
# () (group, backreferenced group)
# | (alternative, or)
# \ (the escape character itself)



#-------------------------------------
# variable_names  %{VARIABLE_NAME} replace with any below
#-------------------------------------
## HTTP Headers
#HTTP_USER_AGENT
#HTTP_REFERER
#HTTP_COOKIE
#HTTP_FORWARDED
#HTTP_HOST
#HTTP_PROXY_CONNECTION
#HTTP_ACCEPT
##Connection Variables
#REMOTE_ADDR
#REMOTE_HOST
#REMOTE_USER
#REMOTE_IDENT
#REQUEST_METHOD
#SCRIPT_FILENAME
#PATH_INFO
#QUERY_STRING
#AUTH_TYPE
## Server Variables
#DOCUMENT_ROOT
#SERVER_ADMIN
#SERVER_NAME
#SERVER_ADDR
#SERVER_PORT
#SERVER_PROTOCOL
#SERVER_SOFTWARE
    ##Dates and Times
#TIME_YEAR
#TIME_MON
#TIME_DAY
#TIME_HOUR
#TIME_MIN
#TIME_SEC
#TIME_WDAY
#TIME
    ##Special Items
#API_VERSION
#THE_REQUEST
#REQUEST_URI
#REQUEST_FILENAME
#IS_SUBREQ
</pre>
				<?
			}

			?>
			<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
			<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
			<? if ( isset( $badUrls ) ) : ?>
				<script>
				$('#download').click(function(e){
					e.preventDefault();
					var badUrls = '<?= json_encode( $badUrls ) ?>';
					console.log(badUrls);
					$.post( "download.php", { badUrls: badUrls, redirectTo: $('#redirectTo').val() }, function( data ) {
						$('#htaccess-container').html( data ).slideDown();
					});
				});
				</script>
			<? endif ?>
		</div>
	</body>
</html>