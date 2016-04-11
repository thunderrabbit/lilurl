<?php /* index.php ( lilURL implementation ) */

require_once 'includes/conf.php'; // <- site-specific settings
require_once 'includes/lilurl.php'; // <- lilURL class file

$lilurl = new lilURL();
$msg = '';

$alphanum_filter = array(
    'options' => array(
        'regexp' =>  "%[a-zA-Z0-9]*%"
    ),
);

// if the form has been submitted
if ( isset($_POST['longurl']) )
{
	$longurl = filter_input(INPUT_POST, 'longurl', FILTER_VALIDATE_URL);
	if(empty($longurl))
	{
		$url_ok = false;
	}
	else
	{
		$url_ok = true;

	    	$longurl = trim($longurl);
	        $manual_id = filter_input(INPUT_POST, 'manual_id', FILTER_VALIDATE_REGEXP, $alphanum_filter);

	    	// set the protocol to not ok by default
	    	$protocol_ok = false;
	    	$string_ok = false;
	    	$url_len_ok = false;
	    	$manual_id_len_ok = false;

	    	$url_len = strlen($longurl);
	    	$url_len_ok = (29 < $url_len && $url_len < 220);

	    	$manual_id_len = strlen($manual_id);
	    	$manual_id_len_ok = ($manual_id_len < 25);

	    	// if there's a list of allowed protocols, 
	    	// check to make sure that the user's url uses one of them
	    	if ( count($allowed_protocols) )
	    	{
	    		foreach ( $allowed_protocols as $ap )
	    		{
	    			if ( strtolower(substr($longurl, 0, strlen($ap))) == strtolower($ap) )
	    			{
	    				$protocol_ok = true;
	    				break;
	    			}
	    		}
	    	}
	    	else // if there's no protocol list, screw all that
	    	{
	    		$protocol_ok = true;
	    	}

	    	// if there's a list of allowed strings, check to make sure they are included in the URL
	    	if(count($allowed_strings))
	    	{
	    		foreach ($allowed_strings as $as)
	    		{
	    			if ( strpos  ( strtolower($longurl), strtolower($as)))
	    			{
	    				$string_ok = true;
	    				break;
	    			}
	    		}
	    	}
	    	else	// no strings to check so don't worry
	    	{
	    		$string_ok = true;
	    	}
	}

	// errorcheckin'
	if (!$url_ok)
	{
		$msg = '<p class="error">Invalid URL</p>';
	}
	elseif (!$url_len_ok)
	{
		$msg = '<p class="error">You call that a URL???</p>';
	}
	elseif (!$manual_id_len_ok)
	{
		$msg = '<p class="error">ids are meant to be short!</p>';
	}
	elseif (!$string_ok)
	{
		$msg = "<p class='error'>Sorry!  URLs only work for specific cases, such as $allowed_strings[0]</p>";
	}
	elseif ( !$protocol_ok )
	{
		$msg = '<p class="error">Invalid protocol!</p>';
	}	

	// Looks good above!  Add the url to the database
	elseif ( $protocol_ok && $string_ok && $lilurl->add_url($longurl,$manual_id,$msg) )
	{
		if ( REWRITE ) // mod_rewrite style link
		{
			$url = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).$lilurl->get_id($longurl);
		}
		else // regular GET style link
		{
			$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?id='.$lilurl->get_id($longurl);
		}

		// if there's a $msg returned by add_url, it was a non-critical error.  pass it on.
		if($msg)
		{ 
			$msg = "<p class='error'>" . $msg . "</p>";
		}

		$msg .= '<p class="success">Your lil&#180; URL is: <a href="'.$url.'">'.$url.'</a></p>';
	}
	else
	{
		$msg = '<p class="error">Creation of your lil&#180; URL failed for some reason.</p>';
	}
}
else // if the form hasn't been submitted, look for an id to redirect to
{
	if ( isset($_GET['id']) ) // check GET first
	{
	        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_REGEXP, $alphanum_filter);
//		if(!empty($id))
//		  {
//		    $id = mysql_real_escape_string($id);
//		  }
	}
	elseif ( isset($_POST['id']) ) // check POST as well
	{
	        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_REGEXP, $alphanum_filter);
//		if(!empty($id))
//		  {
//		    $id = mysql_real_escape_string($id);
//		  }
	}
	elseif ( REWRITE ) // check the URI if we're using mod_rewrite
	{
		$explodo = explode('/', $_SERVER['REQUEST_URI']);
		$id = filter_var($explodo[count($explodo)-1], FILTER_VALIDATE_REGEXP, $alphanum_filter);
//		$id = mysql_real_escape_string($id);
	}
	else // otherwise, just make it empty
	{
		$id = '';
	}

	// if the id isn't empty and it's not this file, redirect to its url
	if ( $id != '' && $id != basename($_SERVER['PHP_SELF']) )
	{
		$location = $lilurl->get_url($id);
		
		if ( $location != -1 )
		{
			header('Location: '.$location);
		}
		else
		{
			$msg = '<p class="error">Sorry, but that lil&#180; URL isn\'t in our database.</p>';
		}
	}
}

// after all that, look up the next default id
$next_id = $lilurl->get_next_id();

// print the form

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
	<head>
		<title><?php echo PAGE_TITLE; ?></title>
		
		<style type="text/css">
		body {
			font: .8em "Trebuchet MS", Verdana, Arial, Sans-Serif;
			text-align: center;
			color: #333;
			background-color: #fff;
			margin-top: 5em;
		}
		
		h1 {
			font-size: 2em;
			padding: 0;
			margin: 0;
		}

		h4 {
			font-size: 1em;
			font-weight: bold;
		}
		
		form {
			width: 28em;
			background-color: #eee;
			border: 1px solid #ccc;
			margin-left: auto;
			margin-right: auto;
			padding: 1em;
		}

		fieldset {
			border: 0;
			margin: 0;
			padding: 0.5em;
		}
		
		a {
			color: #09c;
			text-decoration: none;
			font-weight: bold;
		}

		a:visited {
			color: #07a;
		}

		a:hover {
			color: #c30;
		}

		.error, .success {
			font-size: 1.2em;
			font-weight: bold;
		}
		
		.error {
			color: #ff0000;
		}
		
		.success {
			color: #000;
		}
		
		</style>

	</head>
	
	<body onload="document.getElementById('longurl').focus()">
		
		<h1><?php echo PAGE_TITLE; ?></h1>
		
		<?php echo $msg; ?>
		
		<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
			<fieldset>
				<label for="longurl">Enter a long URL:</label>
				<input type="text" name="longurl" id="longurl" />
			</fieldset>
			<fieldset>
				<label for="manual_id">Desired short id:</label>
				<input type="text" name="manual_id" id="manual_id" /> next: <?=$next_id?>
			</fieldset>
			<fieldset>
				<input type="submit" name="submit" id="submit" value="Make it lil&#180;!" />
			</fieldset>
		</form>&nbsp;

		<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
			<fieldset>
				<label for="id">Or enter an id:</label>
				<input type="text" name="id" id="id" />
				<input type="submit" name="submit" id="submit" value="Find its URL!" />
			</fieldset>
		</form>
		<h4>Powered by a <a href="https://github.com/thunderrabbit/lilurl/">modified</a> version of <a href="http://lilurl.sourceforge.net">lilURL</a></h4>
	</body>
</html>
