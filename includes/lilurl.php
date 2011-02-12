<?php /* lilurl.php ( lilURL class file ) */

class lilURL
{
	// constructor
	function lilURL()
	{
		// open mysql connection
		mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS) or die('Could not connect to database');
		mysql_select_db(MYSQL_DB) or die('Could not select database');	
	}

	// return the id for a given url (or -1 if the url doesn't exist)
	function get_id($url)
	{
		$q = 'SELECT id FROM '.URL_TABLE.' WHERE (url="'.$url.'")';
		$result = mysql_query($q);

		if ( mysql_num_rows($result) )
		{
			$row = mysql_fetch_array($result);
			return $row['id'];
		}
		else
		{
			return -1;
		}
	}

	// return the url for a given id (or -1 if the id doesn't exist)
	function get_url($id)
	{
		$q = 'SELECT url FROM '.URL_TABLE.' WHERE (id="'.$id.'")';
		$result = mysql_query($q);

		if ( mysql_num_rows($result) )
		{
			$row = mysql_fetch_array($result);
			return $row['url'];
		}
		else
		{
			return -1;
		}
	}
	
	// add a url to the database
	function add_url($url, $manual_id = NULL, &$msg = NULL)
	{
		// check to see if the url's already in there
		$id = $this->get_id($url);
		
		// if it is, return true
		if ( $id != -1 )
		{
			$msg = "That URL already has a lil' URL:";
			return true;
		}
		else // otherwise, put it in
		{
		
			if($manual_id)
			{
				if($this->get_url($manual_id) == -1)
				{
					$manual = 'true';
				}
				else
				{
					$msg = $manual_id . " already used in DB.";
					$manual = 'false';
				}
			}
			else
			{
				$manual = 'false';
			}

			// according to what happened above, test to see if we will use the user-provided manual_id or not
			if($manual == 'true')
			{
				$id = $manual_id;
			}
			else
			{
				$id = $this->get_next_id();
			}

			$q = 'INSERT INTO '.URL_TABLE.' (id, url, manual, date) VALUES ("'.$id.'", "'.$url.'", "'.$manual.'", NOW())';

			return mysql_query($q);
		}
	}

	// return the most recent id (or -1 if no ids exist)
	function get_last_id()
	{	
		$q = 'SELECT id FROM '.URL_TABLE.' where manual = "false" ORDER BY date DESC   LIMIT 1';
		$result = mysql_query($q);

		if ( mysql_num_rows($result) )
		{
			$row = mysql_fetch_array($result);
			return $row['id'];
		}
		else
		{
			return -1;
		}
	}	

	// return the next id
	function get_next_id($last_id = NULL)
	{

		// if the last id is NULL(not sent), then look to DB
		if($last_id == NULL)
		{
			$last_id = $this->get_Last_id();
		}

		// if the last id is -1 (non-existant), start at the begining with 0
		if ( $last_id == -1 )
		{
			$next_id = 0;
		}
		else
		{
			// loop through the id string until we find a character to increment
			for ( $x = 1; $x <= strlen($last_id); $x++ )
			{
				$pos = strlen($last_id) - $x;

				if ( $last_id[$pos] != 'z' )
				{
					$next_id = $this->increment_id($last_id, $pos);
					break; // <- kill the for loop once we've found our char
				}
			}

			// if every character was already at its max value (z),
			// append another character to the string
			if ( !isSet($next_id) )
			{
				$next_id = $this->append_id($last_id);
			}
		}

		// check to see if the $next_id we made already exists, and if it does, 
		// loop the function until we find one that doesn't
		//
		// (this is basically a failsafe to get around the potential dangers of
		//  my kludgey use of a timestamp to pick the most recent id)
		$q = 'SELECT id FROM '.URL_TABLE.' WHERE (id="'.$next_id.'")';
		$result = mysql_query($q);
		
		if ( mysql_num_rows($result) )
		{
			$next_id = $this->get_next_id($next_id);
		}

		return $next_id;
	}

	// make every character in the string 0, and then add an additional 0 to that
	function append_id($id)
	{
		for ( $x = 0; $x < strlen($id); $x++ )
		{
			$id[$x] = 0;
		}

		$id .= 0;

		return $id;
	}

	// increment a character to the next alphanumeric value and return the modified id
	function increment_id($id, $pos)
	{		
		$char = $id[$pos];
		
		// add 1 to numeric values
		if ( is_numeric($char) )
		{
			if ( $char < 9 )
			{
				$new_char = $char + 1;
			}
			else // if we're at 9, it's time to move to the alphabet
			{
				$new_char = 'a';
			}
		}
		else // move it up the alphabet
		{
			$new_char = chr(ord($char) + 1);
		}

		$id[$pos] = $new_char;
		
		// set all characters after the one we're modifying to 0
		if ( $pos != (strlen($id) - 1) )
		{
			for ( $x = ($pos + 1); $x < strlen($id); $x++ )
			{
				$id[$x] = 0;
			}
		}

		return $id;
	}

}

function print_rob($what)
{
	echo "<pre>";
	print_r($what);
	echo "</pre>";
}
?>
