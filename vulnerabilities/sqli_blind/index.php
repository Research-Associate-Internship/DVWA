<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT.'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated', 'phpids' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ]   = 'Vulnerability: SQL Injection (Blind)'.$page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'sqli_blind';
$page[ 'help_button' ]   = 'sqli_blind';
$page[ 'source_button' ] = 'sqli_blind';

dvwaDatabaseConnect();

$vulnerabilityFile = '';
switch( $_COOKIE[ 'security' ] ) {
	case 'low':
		$vulnerabilityFile = 'low.php';
		break;
	case 'medium':
		$vulnerabilityFile = 'medium.php';
		break;
	case 'high':
		$vulnerabilityFile = 'high.php';
		break;
	default:
		$vulnerabilityFile = 'impossible.php';
		break;
}

require_once DVWA_WEB_PAGE_TO_ROOT."vulnerabilities/sqli_blind/source/{$vulnerabilityFile}";

// Anti-CSRF
if( $vulnerabilityFile == 'impossible.php' )
	generateTokens();

// Check if Magic Quotes are on or off
$magicQuotesWarningHtml = '';
if( ini_get( 'magic_quotes_gpc' ) == true ) {
	$magicQuotesWarningHtml = "<div class=\"warning\">Magic Quotes are on, you will not be able to inject SQL.</div>";
}

$method = 'GET';
if( $vulnerabilityFile == 'medium.php' )
	$method = 'POST';

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Vulnerability: SQL Injection (Blind)</h1>

	{$magicQuotesWarningHtml}

	<div class=\"vulnerable_code_area\">";
if( $vulnerabilityFile == 'high.php' ){
	$page[ 'body' ] .= "Click <a href=\"#\" onClick=\"javascript:popUp('cookie-input.php');return false;\">here to change your ID</a>.";
}
else {
	$page[ 'body' ] .= "
		<form action=\"#\" method=\"{$method}\">
			<p>
				User ID:";
	if( $vulnerabilityFile == 'medium.php' ){
		$page[ 'body' ] .= "				<select name=\"id\">";
		$query  = "SELECT COUNT(*) FROM users;";
		$result = mysql_query( $query ) or die( '<pre>' . mysql_error() . '</pre>' );
		$num    = mysql_result( $result, 0 );
		$i      = 0;
		while( $i < $num ) { $i++; $page[ 'body' ] .= "<option value=\"{$i}\">{$i}</option>"; }
		$page[ 'body' ] .= "</select>";
	}
	else
		$page[ 'body' ] .= "				<input type=\"text\" size=\"15\" name=\"id\">";

	$page[ 'body' ] .= "				<input type=\"submit\" name=\"Submit\" value=\"Submit\">
			</p>"
;

	if( $vulnerabilityFile == 'impossible.php' )
		$page[ 'body' ] .= "			" . tokenField();

	$page[ 'body' ] .= "
		</form>";
}
$page[ 'body' ] .= "
		{$html}
	</div>

	<h2>More Information</h2>
	<ul>
		<li>".dvwaExternalLinkUrlGet( 'http://www.securiteam.com/securityreviews/5DP0N1P76E.html' )."</li>
		<li>".dvwaExternalLinkUrlGet( 'https://en.wikipedia.org/wiki/SQL_injection' )."</li>
		<li>".dvwaExternalLinkUrlGet( 'http://ferruh.mavituna.com/sql-injection-cheatsheet-oku/' )."</li>
		<li>".dvwaExternalLinkUrlGet( 'http://pentestmonkey.net/cheat-sheet/sql-injection/mysql-sql-injection-cheat-sheet' )."</li>
		<li>".dvwaExternalLinkUrlGet( 'https://www.owasp.org/index.php/Blind_SQL_Injection' )."</li>
		<li>".dvwaExternalLinkUrlGet( 'http://bobby-tables.com/' )."</li>
	</ul>
</div>
";

dvwaHtmlEcho( $page );

?>
