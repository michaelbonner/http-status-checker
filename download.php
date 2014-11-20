<?

function prepare_url( $url ) {
	if( $qmarkpos = strpos( $url, '?' ) )
		$url = substr($url, 0, $qmarkpos );

	if( $hashpos = strpos( $url, '#' ) )
		$url = substr($url, 0, $hashpos );

	$url = urldecode( $url );
	$url = preg_quote( $url, '/' );
	if( substr( $url, -1) !== '/' ) {
		$url = $url . '\/?';
	}
	return $url;
}


$urls			= json_decode( $_POST['badUrls'] );
$redirectTo		= $_POST['redirectTo'];

foreach( $urls as $url ) {
	echo "RewriteRule ^" . prepare_url( $url ) . "$ " . $redirectTo . "? [R=301,NC,L]<br />";
}
