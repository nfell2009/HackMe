<?php

	/*
	 * LICENCING. #DoWhatTheFuckYouWant.
	 * That's right.
	*/

	$mysqlHost = "localhost";
	$mysqlUser = "NOT_ROOT";
	$mysqlPass = "password1";
	$mysqlDB   = "HackMe";

	/* Using mysql instead of mysqli because it's shittier */
	$c = mysql_connect($mysqlHost, $mysqlUser, $mysqlPass);
	mysql_select_db($mysqlDB);

	/* Yes. Create the table if it doesn't exist.. Someone can drop it. */
	$result = mysql_query("CREATE TABLE IF NOT EXISTS `Hack_Data` (`ID` int(11) NOT NULL AUTO_INCREMENT,`Text` varchar(256) NOT NULL,PRIMARY KEY (`ID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
	if (!$result) { die("Someone delete it?!\n<br/>" . mysql_error()); }

	/* For the dump button */
	if (isset($_GET['SQL_DATA'])){
		header("Content-Type:text/plain");
		$result = mysql_query("SELECT * FROM `Hack_Data`");
		if(!$result) { echo "MySQL Error."; return; }
		echo "ID : Text\n";
		$i = 0;
		while($row = mysql_fetch_array($result))
  		{
			$i++;
  			echo $row['ID'];
			echo "  : ";
	  		echo $row['Text'];
			echo "\n";
  		}
  		echo "-------------------\n";
		echo "Number of rows: " . $i;
		return;
	}

	/* To get the ID */
	/*
	 * Okay yes, this is made to work with injection, not against.
	 * But the advanced methods are longer 
	 * to explain and this is way easier.
	 */
	if(isset($_GET['GET_ID']) && !empty($_GET['GET_ID'])){
		header("Content-Type:text/plain");
		$query = "SELECT * FROM `Hack_Data` WHERE ID = '" . urldecode($_GET['GET_ID']) . "';";
		$queries = explode(";", $query);
		if(empty($queries)){
			$queries = array( $query );
		}
		$result = mysql_query($queries[0]);
		if(count($queries) > 1){
			for ($i = 1; $i < count($queries); ++$i) {
        			mysql_query($queries[$i]);
    			}
		}
		//echo "SELECT * FROM `Hack_Data` WHERE ID = '" . urldecode($_GET['GET_ID']) . "';";
		if (!$result) { echo "An empty result for : " . $_GET['GET_ID']; return; }
		$row = mysql_fetch_array($result);
		if(empty($row)){
			echo("Empty result for : " . $_GET['GET_ID']);
			return;
		}
		echo $row['ID'];
		echo "  : ";
		echo $row['Text'];
		return;
	}

	/* Do you need me to explain that this handles insert? */
	if(isset($_GET['INSERT']) && !empty($_GET['INSERT'])){
		header("Content-Type:text/plain");
		$result = mysql_query("SELECT COUNT(*) FROM `Hack_Data`;");
		if(!$result) { echo "MySQL Error."; return; }
		if(mysql_fetch_array($result)[0] >= 15){ echo "Max amount!"; return; }
		$result = mysql_query("INSERT INTO `Hack_Data` (`ID`, `Text`) VALUES (NULL, '" . mysql_real_escape_string($_GET['INSERT']) . "');");
                if(!$result) { echo "MySQL Error."; return; }
		echo("Added: " . mysql_real_escape_string($_GET['INSERT']));
		return;
	}

	/* So as you may have noticed. We're using AJAX. BECAUSE WHY NOT BRO! */

?>

<html>
	<header>
		<title>HackMe | SQL</title>
		<!-- Using a CDN to make life easier! -->
		<script src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
		<script src="script.js"></script>
	</header>

	<!-- No more comments from here on. -->
	<body onload="startup();">
		<table>
			<tr>
				<td><input type="text" name="InsertText" placeholder="Random String." id="InsertText"/></td>
				<td><button type="button" name="InsertButton" id="InsertButton" onclick="insert($('#InsertText').val());">INSERT</button></td>
			</tr>
			<tr>
				<td><input type="number " name="IDNumber" placeholder="ID of data." id="IDNumber"/></td>
				<td><button type="button" name="IDButton" id="IDButton" onclick="get_id($('#IDNumber').val());">Get ID</button></td>
			</tr>
			<tr>
				<td><strong>DUMP SQL TABLE.</strong></td>
				<td><button type="button" name="IDButton" id="IDButton" onclick="dump_data();">DUMP</button></td>
			</tr>
		</table>
		<div id="OutputContainer" style="display: none;border-style: solid; width: 40%; word-wrap: break-word;">
			<h4>Output:</h4>
			<div id="InnerContainer" style="border-style: dotted; border-width: small;">
				<pre id="Output">No output!</pre>
			</div>
		</div>
		<div>
			<h3>Instructions.</h3>
			<p>This is very much like a &quot;My First SQL Injection&quot; and it is dramatically simplified.
			<br/>Although still can be used to educate!</p>
			<ol>
				<li>Insert some data into the table. Using INSERT.</li>
				<li>Dump the database to see your data!</li>
				<li>Get the ID of a couple of things..</li>
				<li>Inset the injection into the ID input. <button id="InjectHelp" onclick="insertAssist();">Help.</button></li>
				<li>You've just erased the data from that table!</li>
				<li>Click dump again to see.</li>
			</ol>
		</div>
		<div>
			<h3>Extra Information.</h3>
			<p>This is very much like a &quot;My First SQL Injection&quot; and it is dramatically simplified.
			<br/>Although still can be used to educate!</p>
			<ul>
				<li>INSERT does what you think it would do.</li>
				<li>Dumping is just printing out all the data.</li>
				<li>Getting the ID is getting a certain coloum from that table.</li>
				<li>Injection is the hack, here is the code:</li>
				<li>2';TRUNCATE TABLE &#96;Hack_Data&#96;;'
					<hr/>Lets talk through this.
					<br/>2'; compleats the rest of the command so we can execute the one's we want.
					<br/>TRUNCATE TABLE &#96;Hack_Data&#96; selects all the data and delets it.
					<br/>The next ; compeleats that command and the closing quote makes the statment runnable.
					<br/><br/>With the trunicate the statments look like:
					<br/>SELECT * FROM &#96;Hack_Data&#96; WHERE ID = '2';TRUNCATE TABLE &#96;Hack_Data&#96;;'';<br/>
					<br/>This can be broken down into it's seperate commands like so:
					<br/>SELECT * FROM &#96;Hack_Data&#96; WHERE ID = '2';
					<br/>TRUNCATE TABLE &#96;Hack_Data&#96;;
					<br/>'';
				</li>
			</ul>
		</div>
	</body>
	<!-- I SAID NONE! -->
</html>
