<?php
	
	//O script operations.php serve principalmente para a conexão com o banco dados
	//A variável $op serve para identificar qual função será executada

	$op = $_POST['op'];
	$dbconn = pg_connect("host=localhost dbname=projeto user=postgres password=postgres") or die('Could notconnect: ' . pg_last_error());

	// A função executada quando $op == "1" é quem auxilia a função UpSearch no javascript   
	if ($op == "1") {
		$aluno = preg_replace('/[^0-9,.]/', '',$_POST['aluno']);
		if ($_POST['relid'] != "n"){
			$relid = preg_replace('/[^0-9,.]/', '',$_POST['relid']);
		}else{
			$relid = "n";
		}
		
		$sql = "SELECT ori.id, rel.id, alu.nome FROM rel rel INNER JOIN pessoa alu ON rel.id_aluno = alu.id INNER JOIN pessoa ori ON rel.id_orientador = ori.id WHERE rel.id_aluno = ".$aluno." ORDER BY rel.tipo;";	
		$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
		$numrows = pg_num_rows($result);
		$arro = array();
		$alu_nome = "";
		for ($i = 0; $i < $numrows; $i++) {
			$line = pg_fetch_row($result, $i);
			$alu_nome = ucwords($line[2]);
			$line_data = array("ori_id"=>$line[0], "rel_id"=>$line[1]);
			array_push($arro, $line_data);
		}
		if ($relid != "n"){
			$sql = "SELECT tipo, id_aluno, ano FROM rel WHERE id = ".$relid." ;";
			$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
			$line_data = pg_fetch_row($result, 0);
			$tipo = $line_data[0];
			$id_aluno_anterior = $line_data[1];
			$ano = $line_data[2];
		} else {
			$tipo = "n";
			$id_aluno_anterior = "n";
			$ano = "n";
		}
		if ($numrows == 0) {
			$sql = "SELECT nome FROM pessoa WHERE id = ".$aluno." ;";
			$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
			$line_data = pg_fetch_row($result, 0);
			$alu_nome = ucwords($line_data[0]);
		}
		$table_data = array($alu_nome, $arro, $tipo, $id_aluno_anterior, $ano);
		echo json_encode($table_data);
	}

	// A função executada quando $op == "2" é quem auxilia a função DownSearch no javascript   
	if ($op == "2") {
		$orientador = preg_replace('/[^0-9,.]/', '',$_POST['orientador']);
		if ($_POST['relid'] != "n"){
			$relid = preg_replace('/[^0-9,.]/', '',$_POST['relid']);
		}else{
			$relid = "n";
		}
		$sql = "SELECT alu.id, rel.id, ori.nome FROM rel rel INNER JOIN pessoa alu ON rel.id_aluno = alu.id INNER JOIN pessoa ori ON rel.id_orientador = ori.id WHERE rel.id_orientador = ".$orientador." ORDER BY rel.tipo;";	
		$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
		$numrows = pg_num_rows($result);
		$arra = array();
		$ori_nome = "";
		for ($i = 0; $i < $numrows; $i++) {
			$line = pg_fetch_row($result, $i);
			$ori_nome = ucwords($line[2]);
			$line_data = array("alu_id"=>ucwords($line[0]), "rel_id"=>$line[1]);
			array_push($arra, $line_data);
		}
		if ($relid != "n"){
			$sql = "SELECT tipo, id_orientador, ano FROM rel WHERE id = ".$relid." ;";
			$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
			$line_data = pg_fetch_row($result, 0);
			$tipo = $line_data[0];
			$id_orientador_anterior = $line_data[1];
			$ano = $line_data[2];
		} else {
			$tipo = "n";
			$id_orientador_anterior = "n";
			$ano = "n";
		}
		if ($numrows == 0) {
			$sql = "SELECT nome FROM pessoa WHERE id = ".$orientador." ;";
			$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
			$line_data = pg_fetch_row($result, 0);
			$ori_nome = ucwords($line_data[0]);
		}
		$table_data = array($ori_nome, $arra, $tipo, $id_orientador_anterior, $ano);
		echo json_encode($table_data);
	}
	// A função executada quando $op == "3" é utilizada para a busca por nomes   
	if ($op == "3") {
		$str = $_POST['str'];
		$sql = "SELECT id, nome FROM pessoa WHERE text_vector @@ plainto_tsquery('".strtolower($str)."');";	
		$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
		$numrows = pg_num_rows($result);
		$arrnomes = array();
		for ($i = 0; $i < $numrows; $i++) {
			$line = pg_fetch_row($result, $i);
			$line_data = array("id"=>$line[0], "nome"=>ucwords($line[1]));
			array_push($arrnomes, $line_data);
		}
		echo json_encode($arrnomes);
	}
	// A função executada quando $op == "4" é utilizada para a busca por maiores detalhes de um documento 
	if ($op == "4") {
		if ($_POST['relid'] != "n"){
			$relid = preg_replace('/[^0-9,.]/', '',$_POST['relid']);
		}else{
			$relid = "n";
		}
		$sql = "SELECT alu.nome, ori.nome, rel.tipo, rel.ano, rel.url, rel.citation, rel.topic, rel.author_lattes, rel.network_acronym_str, rel.network_name_str, rel.title FROM rel rel INNER JOIN pessoa alu ON rel.id_aluno = alu.id INNER JOIN pessoa ori ON rel.id_orientador = ori.id WHERE rel.id = ".$relid.";";
		$result = pg_query($sql) or die('Query failed: ' . pg_last_error());
		$line = pg_fetch_row($result, 0);
		$line_data = array("aluno"=>ucwords($line[0]), "orientador"=>ucwords($line[1]), "tipo"=>$line[2], "ano"=>$line[3], "url"=>$line[4], "citation"=>$line[5], "topic"=>$line[6], "author_lattes"=>$line[7], "network_acronym_str"=>$line[8], "network_name_str"=>$line[9], "title"=>$line[10]);
		echo json_encode($line_data);
	}
	// A função executada quando $op == "5" é utilizada para verificar se um determinado ID existe
	if ($op == "5") {
		$id = preg_replace('/[^0-9,.]/', '',$_POST['id']);
		$sql = "SELECT id, nome FROM pessoa WHERE id = ".$id." ;";
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