<?php
	$pw = $_GET['pw'];
	if ($pw == "qazxsw") {
		set_time_limit(1000000);
		ob_start();

		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$dbconn = pg_connect("host=localhost dbname=projeto user=postgres password=postgres") or die('Could notconnect: ' . pg_last_error());
		
		echo "<br>EXCLUIR TABELAS PRINCIPAIS E BRUTA<br>";
		echo "->DROP TABLE rel<br>";
		ob_flush();

		pg_query("DROP TABLE IF EXISTS rel; DROP SEQUENCE IF EXISTS rel_id_seq;");

		echo "->DROP TABLE pessoa<br>";
		ob_flush();

		pg_query("DROP TABLE IF EXISTS pessoa; DROP SEQUENCE IF EXISTS pessoa_id_seq;");

		echo "->DROP TABLE pesquisa<br>";
		ob_flush();

		pg_query("DROP TABLE IF EXISTS pesquisa;");

		echo "<br>CRIAÇÃO, PREENCHIMENTO E PROCESSO DE LIMPEZA NA TABELA \"BRUTA\"<br>";
		echo "->CREATE TABLE pesquisa<br>";
		ob_flush();

		pg_query("
			CREATE TABLE public.pesquisa
			(
			    id character varying COLLATE pg_catalog.\"default\" NOT NULL,
			    oai_identifier_str character varying COLLATE pg_catalog.\"default\",
			    network_acronym_str character varying COLLATE pg_catalog.\"default\",
			    network_name_str character varying COLLATE pg_catalog.\"default\",
			    title character varying COLLATE pg_catalog.\"default\",
			    author character varying COLLATE pg_catalog.\"default\",
			    author2 character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.advisor1.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.advisor2.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.advisor-co1.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.advisor-co2.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee1.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee2.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee3.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee4.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee5.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.authorLattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.advisor1Lattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.advisor2Lattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.advisor-co1Lattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.advisor-co2Lattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee1Lattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee2Lattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee3Lattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee4Lattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.contributor.referee5Lattes.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    topic character varying COLLATE pg_catalog.\"default\",
			    \"dc.identifier.citation.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    language character varying COLLATE pg_catalog.\"default\",
			    \"dc.rights.driver.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    \"dc.relation.none.fl_str_mv\" character varying COLLATE pg_catalog.\"default\",
			    data character varying COLLATE pg_catalog.\"default\",
			    format character varying COLLATE pg_catalog.\"default\",
			    url character varying COLLATE pg_catalog.\"default\",
			    CONSTRAINT pesquisa_pkey PRIMARY KEY (id)
			)
			WITH (
			    OIDS = FALSE
			)
			TABLESPACE pg_default;

			ALTER TABLE public.pesquisa
			    OWNER to postgres;
			");

		echo "->COPY pesquisa<br>";
		ob_flush();

		pg_query("
			COPY pesquisa
			FROM 'C:\\Program Files\\PostgreSQL\\10\\bdtd_230418.csv' DELIMITER ',' CSV HEADER;
			");
		//pg_query("INSERT INTO pesquisa 
				//SELECT * FROM pesquisa2 limit 10000");

		////home/gabrielm/Downloads/bdtd_230418.csv
		///C:\\Program Files\\PostgreSQL\\10\\bdtd_230418.csv
		

		echo "CORRECAO -> Tirar [UNESP] author<br>";
		ob_flush();

		$temp_result = pg_query(
			"SELECT id, author FROM pesquisa WHERE author LIKE '% [UNESP]%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode(' [UNESP]',$line_data[1]);
			$insert_line_data = $aux[0];
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE pesquisa
					SET author = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		echo "CORRECAO -> Tirar [UNESP] dc.contributor.advisor1.fl_str_mv <br>";
		ob_flush();

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM pesquisa WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '% [UNESP]%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode(' [UNESP]',$line_data[1]);
			$insert_line_data = $aux[0];
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE pesquisa
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		echo "CORRECAO -> Tirar [UNIFESP] author<br>";
		ob_flush();

		$temp_result = pg_query(
			"SELECT id, author FROM pesquisa WHERE author LIKE '% [UNIFESP]%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode(' [UNIFESP]',$line_data[1]);
			$insert_line_data = $aux[0];
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE pesquisa
					SET author = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		echo "CORRECAO -> Tirar [UNIFESP] dc.contributor.advisor1.fl_str_mv";
		ob_flush();

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM pesquisa WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '% [UNIFESP]%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode(' [UNIFESP]',$line_data[1]);
			$insert_line_data = $aux[0];
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE pesquisa
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}
		
		echo "CORRECAO -> Tirar Orientador dc.contributor.advisor1.fl_str_mv <br>";
		ob_flush();

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM pesquisa WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '%Orientador:%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$insert_line_data = trim(str_replace("Orientador:","",$line_data[1]));
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE pesquisa
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		echo "CORRECAO -> Tirar Co-orientador dc.contributor.advisor1.fl_str_mv";
		ob_flush();

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM pesquisa WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '%Co-orientador:%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode('Co-orientador:',$line_data[1]);
			$insert_line_data = trim($aux[0]);
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE pesquisa
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		echo "CORRECAO -> Corrigir virgulas author <br>";
		ob_flush();

		$temp_result = pg_query(
			"SELECT id, author FROM pesquisa WHERE author LIKE '%,%'" 
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode('\,',$line_data[1]);
			if (isset($aux[1])) {
				$insert_line_data = trim($aux[1])." ".trim($aux[0]);
			}else{
				$insert_line_data = trim($aux[0]);
			}
			
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE pesquisa
					SET author = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		echo "CORRECAO -> Corrigir virgulas dc.contributor.advisor1.fl_str_mv <br>";
		ob_flush();

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM pesquisa WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '%,%'" 
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode('\,',$line_data[1]);
			if (isset($aux[1])) {
				$insert_line_data = trim($aux[1])." ".trim($aux[0]);
			}else{
				$insert_line_data = trim($aux[0]);
			}
			
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE pesquisa
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}


		echo "CORRECAO -> Aplicar a função LOWER() em author<br>";
		ob_flush();

		pg_query("UPDATE pesquisa SET author = LOWER(author)");

		echo "CORRECAO -> Aplicar a função LOWER() em dc.contributor.advisor1.fl_str_mv<br>";
		ob_flush();

		pg_query("UPDATE pesquisa SET \"dc.contributor.advisor1.fl_str_mv\" = LOWER(\"dc.contributor.advisor1.fl_str_mv\")");


		echo "CORRECAO -> Remover duplicatas<br>";
		ob_flush();

		$temp_result = pg_query("
				SELECT * FROM (
				  SELECT id, title, author, \"dc.contributor.advisor1.fl_str_mv\",
				  ROW_NUMBER() OVER(PARTITION BY title, author, \"dc.contributor.advisor1.fl_str_mv\" ORDER BY id asc) AS Row
				  FROM pesquisa
				) dups
				WHERE dups.Row > 1 
				");
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			pg_query("
					DELETE FROM pesquisa WHERE (id != '".$line_data[0]."') AND (title like '".str_replace("'","_",$line_data[1])."') AND (author like '".str_replace("'","_",$line_data[2])."') AND (\"dc.contributor.advisor1.fl_str_mv\" like '".str_replace("'","_",$line_data[3])."')
				");
		}


		echo "CORRECAO -> masterThesis -> m<br>";
		ob_flush();

		pg_query("
				UPDATE pesquisa
				SET format = 'm'
				WHERE format = 'masterThesis'
				");


		echo "CORRECAO -> doctoralThesis -> d<br>";
		ob_flush();

		pg_query("
				UPDATE pesquisa
				SET format = 'd'
				WHERE format = 'doctoralThesis'
				");
	

		echo "<br>RECRIAÇÃO DAS TABELAS PRINCIPAIS<br>";
		echo "->CREATE TABLE pessoa<br>";
		ob_flush();

		pg_query("
			CREATE SEQUENCE IF NOt EXISTS pessoa_id_seq;
			CREATE TABLE public.pessoa
			(
			    id integer NOT NULL DEFAULT nextval('pessoa_id_seq'::regclass),
			    nome character varying(255) COLLATE pg_catalog.\"default\" NOT NULL,
			    CONSTRAINT pessoa_pkey PRIMARY KEY (id)
			)
			WITH (
			    OIDS = FALSE
			)
			TABLESPACE pg_default;

			ALTER TABLE public.pessoa
			    OWNER to postgres;
			");


		echo "->CREATE TABLE rel<br>";
		ob_flush();

		pg_query("
			CREATE SEQUENCE IF NOT EXISTS rel_id_seq; 
			CREATE TABLE public.rel
			(
			    id integer NOT NULL DEFAULT nextval('rel_id_seq'::regclass),
			    id_orientador integer NOT NULL,
			    id_aluno integer NOT NULL,
			    tipo character varying COLLATE pg_catalog.\"default\",
			    ano character varying COLLATE pg_catalog.\"default\",
			    citation character varying COLLATE pg_catalog.\"default\",
			    topic character varying COLLATE pg_catalog.\"default\",
			    author_lattes character varying COLLATE pg_catalog.\"default\",
			    network_acronym_str character varying COLLATE pg_catalog.\"default\",
			    network_name_str character varying COLLATE pg_catalog.\"default\",
			    title character varying COLLATE pg_catalog.\"default\",
			    url character varying COLLATE pg_catalog.\"default\",
			    CONSTRAINT rel_pkey PRIMARY KEY (id),
			    CONSTRAINT foreign_id_aluno FOREIGN KEY (id_aluno)
			        REFERENCES public.pessoa (id) MATCH SIMPLE
			        ON UPDATE NO ACTION
			        ON DELETE NO ACTION,
			    CONSTRAINT foreign_id_orientador FOREIGN KEY (id_orientador)
			        REFERENCES public.pessoa (id) MATCH SIMPLE
			        ON UPDATE NO ACTION
			        ON DELETE NO ACTION
			)
			WITH (
			    OIDS = FALSE
			)
			TABLESPACE pg_default;
			ALTER TABLE public.rel
			    OWNER to postgres;
			");

		echo "<br>PREENCHIMENTO DAS TABELAS PRINCIPAIS A PARTIR DA TABELA BRUTA<br>";
		echo "->INSERT INTO pessoa<br>";
		ob_flush();

		pg_query("
			INSERT INTO pessoa (nome)
			SELECT DISTINCT author as nome FROM pesquisa WHERE author IS NOT NULL
			EXCEPT
			SELECT DISTINCT \"dc.contributor.advisor1.fl_str_mv\" as nome FROM pesquisa WHERE \"dc.contributor.advisor1.fl_str_mv\" IS NOT NULL
			");
		pg_query("
			INSERT INTO pessoa (nome)
			SELECT DISTINCT \"dc.contributor.advisor1.fl_str_mv\" as nome FROM pesquisa WHERE \"dc.contributor.advisor1.fl_str_mv\" IS NOT NULL
			");

		echo "->INSERT INTO rel<br>";
		ob_flush();

		pg_query("
			INSERT INTO rel (id_orientador, id_aluno, tipo, ano, url, citation, topic, author_lattes, network_acronym_str, network_name_str, title)
			SELECT ori.id, alu.id, pe.format, pe.data, pe.url, pe.\"dc.identifier.citation.fl_str_mv\", pe.topic, pe.\"dc.contributor.authorLattes.fl_str_mv\", pe.network_acronym_str, pe.network_name_str, pe.title
			FROM pessoa ori, pessoa alu, pesquisa pe 
			WHERE alu.nome = author
			and ori.nome = \"dc.contributor.advisor1.fl_str_mv\"
			");

	
		echo "->CRIANDO COLUNA E INDICES PARA FULL TEXT SEARCH<br>";
		ob_flush();

		pg_query("
			ALTER TABLE pessoa ADD COLUMN text_vector TSVECTOR;
			UPDATE pessoa SET text_vector = to_tsvector(nome);
			CREATE INDEX idx_text_vector_pessoa ON pessoa USING gin (text_vector);

			ALTER TABLE rel ADD COLUMN text_vector TSVECTOR;
			UPDATE rel SET text_vector = to_tsvector(title||' '||topic);
			CREATE INDEX idx_text_vector_rel ON rel USING gin (text_vector);
			");


		ob_end_flush(); 
		pg_close($dbconn);
		set_time_limit(120);
	}

?>