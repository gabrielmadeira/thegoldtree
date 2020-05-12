<?php

	//	The operations.php script is mainly used to connect to the database
	//	The $op variable is used to identify which function will be executed


	$op = $_POST['op'];
	$dbconn = pg_connect("host=localhost dbname=thegoldtree user=postgres password=postgres") or die('Could notconnect: ' . pg_last_error());

	// The function executed when $op == "1" is the one that helps the UpSearch function in javascript
 
	if ($op == "1") {
		$author = preg_replace('/[^0-9,.]/', '',$_POST['author']);
		if ($_POST['relid'] != "n"){
			$relid = preg_replace('/[^0-9,.]/', '',$_POST['relid']);
		}else{
			$relid = "n";
		}
		
		$sql = "SELECT adv.id, rel.id, aut.name FROM relationship rel INNER JOIN researcher aut ON rel.id_author = aut.id INNER JOIN researcher adv ON rel.id_advisor = adv.id WHERE rel.id_author = ".$author." ORDER BY rel.type;";	
		$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
		$numrows = pg_num_rows($result);
		$arro = array();
		$aut_name = "";
		for ($i = 0; $i < $numrows; $i++) {
			$line = pg_fetch_row($result, $i);
			$aut_name = ucwords($line[2]);
			$line_data = array("adv_id"=>$line[0], "rel_id"=>$line[1]);
			array_push($arro, $line_data);
		}
		if ($relid != "n"){
			$sql = "SELECT type, id_author, year FROM relationship WHERE id = ".$relid." ;";
			$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
			$line_data = pg_fetch_row($result, 0);
			$type = $line_data[0];
			$id_author_previous = $line_data[1];
			$year = $line_data[2];
		} else {
			$type = "n";
			$id_author_previous = "n";
			$ano = "n";
		}
		if ($numrows == 0) {
			$sql = "SELECT name FROM researcher WHERE id = ".$author." ;";
			$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
			$line_data = pg_fetch_row($result, 0);
			$aut_name = ucwords($line_data[0]);
		}
		$table_data = array($aut_name, $arro, $type, $id_author_previous, $year);
		echo json_encode($table_data);
	}

	
	// The function executed when $op == "2" is the one that helps the DownSearch function in javascript

	if ($op == "2") {
		$advisor = preg_replace('/[^0-9,.]/', '',$_POST['advisor']);
		if ($_POST['relid'] != "n"){
			$relid = preg_replace('/[^0-9,.]/', '',$_POST['relid']);
		}else{
			$relid = "n";
		}
		$sql = "SELECT aut.id, rel.id, adv.name FROM relationship rel INNER JOIN researcher aut ON rel.id_author = aut.id INNER JOIN pessoa adv ON rel.id_advisor = adv.id WHERE rel.id_advisor = ".$advisor." ORDER BY rel.type;";	
		$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
		$numrows = pg_num_rows($result);
		$arra = array();
		$adv_name = "";
		for ($i = 0; $i < $numrows; $i++) {
			$line = pg_fetch_row($result, $i);
			$adv_name = ucwords($line[2]);
			$line_data = array("aut_id"=>ucwords($line[0]), "rel_id"=>$line[1]);
			array_push($arra, $line_data);
		}
		if ($relid != "n"){
			$sql = "SELECT type, id_advisor, year FROM relationship WHERE id = ".$relid." ;";
			$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
			$line_data = pg_fetch_row($result, 0);
			$type = $line_data[0];
			$id_advisor_previous = $line_data[1];
			$year = $line_data[2];
		} else {
			$type = "n";
			$id_advisor_previous = "n";
			$year = "n";
		}
		if ($numrows == 0) {
			$sql = "SELECT name FROM researcher WHERE id = ".$advisor." ;";
			$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
			$line_data = pg_fetch_row($result, 0);
			$adv_name = ucwords($line_data[0]);
		}
		$table_data = array($adv_name, $arra, $type, $id_advisor_previous, $year);
		echo json_encode($table_data);
	}
	// The function performed when $op == "3" is used to search for names
	if ($op == "3") {
		$str = $_POST['str'];
		$sql = "SELECT id, name FROM researcher WHERE text_vector @@ plainto_tsquery('".strtolower($str)."') LIMIT 20;";	
		// pg_query("SET default_text_search_config = 'pg_catalog.portuguese';");
		$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
		$numrows = pg_num_rows($result);
		$arrnames = array();
		for ($i = 0; $i < $numrows; $i++) {
			$line = pg_fetch_row($result, $i);
			$line_data = array("id"=>$line[0], "name"=>ucwords($line[1]));
			array_push($arrnames, $line_data);
		}
		echo json_encode($arrnames);
	}
	// The function executed when $op == "4" is used to search for more details in a document
	if ($op == "4") {
		if ($_POST['relid'] != "n"){
			$relid = preg_replace('/[^0-9,.]/', '',$_POST['relid']);
		}else{
			$relid = "n";
		}
		$sql = "SELECT aut.name, adv.name, rel.type, rel.year, rel.url_bdtd, rel.citation, rel.topic, rel.author_lattes, rel.institution_acron, rel.institution_name, rel.title FROM relationship rel INNER JOIN researcher aut ON rel.id_author = aut.id INNER JOIN researcher adv ON rel.id_advisor = adv.id WHERE rel.id = ".$relid.";";
		$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
		$line = pg_fetch_row($result, 0);
		$line_data = array("author"=>ucwords($line[0]), "advisor"=>ucwords($line[1]), "type"=>$line[2], "year"=>$line[3], "url"=>$line[4], "citation"=>$line[5], "topic"=>$line[6], "author_lattes"=>$line[7], "institution_acron"=>$line[8], "institution_name"=>$line[9], "title"=>$line[10]);
		echo json_encode($line_data);
	}
	// The function executed when $ op == "5" is used to check if a given ID exists
	if ($op == "5") {
		$id = preg_replace('/[^0-9,.]/', '',$_POST['id']);
		$sql = "SELECT id, name FROM researcher WHERE id = ".$id." ;";
		$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
		$numrows = pg_num_rows($result);
		$line = pg_fetch_row($result, 0);
		if ($numrows == 1) {
			echo ucwords($line[1]);
		}else{
			echo "0";
		}
	}

	pg_free_result($result);
	pg_close($dbconn);
	
	
?>
