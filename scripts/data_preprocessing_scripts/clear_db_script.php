<?php
		set_time_limit(1000000);

		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$dbconn = pg_connect("host=localhost dbname=thegoldtree user=postgres password=postgres") or die('Could notconnect: ' . pg_last_error());
		
		// DELETE MAIN AND GROSS TABLES
		// ->DROP TABLE relationship

		pg_query("DROP TABLE IF EXISTS relationship; DROP SEQUENCE IF EXISTS relationship_id_seq;");

		// ->DROP TABLE researcher

		pg_query("DROP TABLE IF EXISTS researcher; DROP SEQUENCE IF EXISTS researcher_id_seq;");

		// ->DROP TABLE bdtd_db

		pg_query("DROP TABLE IF EXISTS bdtd_db;");

		// CREATION, COMPLETION AND CLEANING PROCESS IN GROSS TABLE
		// ->CREATE TABLE bdtd_db

		pg_query("
		
			CREATE TABLE public.bdtd_db
			(
			  id character varying NOT NULL,
			  network_acronym_str character varying,
			  network_name_str character varying,
			  title character varying,
			  author character varying,
			  \"dc.contributor.advisor1.fl_str_mv\" character varying,
			  \"dc.contributor.authorLattes.fl_str_mv\" character varying,
			  \"dc.contributor.advisor1Lattes.fl_str_mv\" character varying,
			  topic character varying,
			  \"dc.identifier.citation.fl_str_mv\" character varying,
			  \"dc.publisher.program.fl_str_mv\" character varying,
			  language character varying,
			  \"publishDate\" character varying,
			  format character varying,
			  url character varying,
			  description character varying,
			  CONSTRAINT id_primary_key PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.bdtd_db
			  OWNER TO postgres;
		
		");

		// ->COPY bdtd_db

		pg_query("
			COPY bdtd_db
			FROM '/home/gabriel/dev/ginfo/bdtd_220420.csv' DELIMITER ',' CSV HEADER;
			");

		// CORRECTION -> Remove [UNESP] author

		$temp_result = pg_query(
			"SELECT id, author FROM bdtd_db WHERE author LIKE '% [UNESP]%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode(' [UNESP]',$line_data[1]);
			$insert_line_data = $aux[0];
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE bdtd_db
					SET author = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		// CORRECTION -> Remove [UNESP] dc.contributor.advisor1.fl_str_mv 

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM bdtd_db WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '% [UNESP]%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode(' [UNESP]',$line_data[1]);
			$insert_line_data = $aux[0];
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE bdtd_db
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}
		

		// CORRECTION -> Remove [UNIFESP] author 

		$temp_result = pg_query(
			"SELECT id, author FROM bdtd_db WHERE author LIKE '% [UNIFESP]%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode(' [UNIFESP]',$line_data[1]);
			$insert_line_data = $aux[0];
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE bdtd_db
					SET author = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		// CORRECTION -> Remove [UNIFESP] dc.contributor.advisor1.fl_str_mv

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM bdtd_db WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '% [UNIFESP]%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode(' [UNIFESP]',$line_data[1]);
			$insert_line_data = $aux[0];
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE bdtd_db
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}
		
		// CORRECTION -> Remove 'Orientador' dc.contributor.advisor1.fl_str_mv 

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM bdtd_db WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '%Orientador:%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$insert_line_data = trim(str_replace("Orientador:","",$line_data[1]));
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE bdtd_db
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		// CORRECTION -> Remove 'Co-orientador' dc.contributor.advisor1.fl_str_mv

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM bdtd_db WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '%Co-orientador:%'"
			);
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			$aux = explode('Co-orientador:',$line_data[1]);
			$insert_line_data = trim($aux[0]);
			$insert_line_data = str_replace("'","",$insert_line_data);
			pg_query("UPDATE bdtd_db
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		// CORRECTION -> Correct commas author
		
		$temp_result = pg_query(
			"SELECT id, author FROM bdtd_db WHERE author LIKE '%,%'" 
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
			pg_query("UPDATE bdtd_db
					SET author = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}

		// CORRECTION -> Correct commas dc.contributor.advisor1.fl_str_mv 

		$temp_result = pg_query(
			"SELECT id, \"dc.contributor.advisor1.fl_str_mv\" FROM bdtd_db WHERE \"dc.contributor.advisor1.fl_str_mv\" LIKE '%,%'" 
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
			pg_query("UPDATE bdtd_db
					SET \"dc.contributor.advisor1.fl_str_mv\" = '".$insert_line_data."'
					WHERE id = '".$line_data[0]."'");
		}


		// CORRECTION -> Apply the LOWER () function in author

		pg_query("UPDATE bdtd_db SET author = LOWER(author)");

		// CORRECTION -> Apply the LOWER () function in dc.contributor.advisor1.fl_str_mv

		pg_query("UPDATE bdtd_db SET \"dc.contributor.advisor1.fl_str_mv\" = LOWER(\"dc.contributor.advisor1.fl_str_mv\")");


		// CORRECTION -> Remove duplicates
		
		$temp_result = pg_query("
				SELECT * FROM (
				  SELECT id, title, author, \"dc.contributor.advisor1.fl_str_mv\",
				  ROW_NUMBER() OVER(PARTITION BY title, author, \"dc.contributor.advisor1.fl_str_mv\" ORDER BY id asc) AS Row
				  FROM bdtd_db
				) dups
				WHERE dups.Row > 1 
				");
		$numrows = pg_num_rows($temp_result);
		for ($i = 0; $i < $numrows; $i++) {
			$line_data = pg_fetch_row($temp_result, $i);
			pg_query("
					DELETE FROM bdtd_db WHERE (id != '".$line_data[0]."') AND (title like '".str_replace("'","_",$line_data[1])."') AND (author like '".str_replace("'","_",$line_data[2])."') AND (\"dc.contributor.advisor1.fl_str_mv\" like '".str_replace("'","_",$line_data[3])."')
				");
		}


		// CORRECTION -> masterThesis -> m

		pg_query("
				UPDATE bdtd_db
				SET format = 'm'
				WHERE format = 'masterThesis'
				");


		// CORRECTION -> doctoralThesis -> d

		pg_query("
				UPDATE bdtd_db
				SET format = 'd'
				WHERE format = 'doctoralThesis'
				");
				
		// ADD URL_BDTD COLUMN
		
		pg_query("
				ALTER TABLE bdtd_db ADD COLUMN url_bdtd character varying;
				UPDATE bdtd_db SET url_bdtd = concat_ws('', 'http://bdtd.ibict.br/vufind/Record/', id);
				");
	

		// RECREATION OF THE MAIN TABLES
		// ->CREATE TABLE researcher

		pg_query("
			CREATE SEQUENCE IF NOt EXISTS researcher_id_seq;
			
			CREATE TABLE public.researcher
			(
			  id integer NOT NULL DEFAULT nextval('researcher_id_seq'::regclass),
			  name character varying,
			  CONSTRAINT id_primary_key_researcher PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.researcher
			  OWNER TO postgres;
			
			");


		// CREATE TABLE relationship

		pg_query("
			CREATE SEQUENCE IF NOT EXISTS relationship_id_seq; 
			
			CREATE TABLE public.relationship
			(
			  id integer NOT NULL DEFAULT nextval('relationship_id_seq'::regclass),
			  id_advisor integer,
			  id_author integer,
			  type character varying,
			  year character varying,
			  citation character varying,
			  abstract character varying,
			  topic character varying,
			  url character varying,
			  url_bdtd character varying,
			  institution_acron character varying,
			  institution_name character varying,
			  title character varying,
			  author_lattes character varying,
			  CONSTRAINT id_primary_key_relationship PRIMARY KEY (id),
			  CONSTRAINT id_advisor_foreign_key_relationship FOREIGN KEY (id_advisor)
				  REFERENCES public.researcher (id) MATCH SIMPLE
				  ON UPDATE NO ACTION ON DELETE NO ACTION,
			  CONSTRAINT id_author_foreign_key_relationship FOREIGN KEY (id_author)
				  REFERENCES public.researcher (id) MATCH SIMPLE
				  ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);
			ALTER TABLE public.relationship
			  OWNER TO postgres;
			
			");

		// FILLING THE MAIN TABLES FROM THE GROSS TABLE
		// INSERT INTO researcher
		

		pg_query("
			INSERT INTO researcher (name)
			SELECT DISTINCT author as name FROM bdtd_db WHERE author IS NOT NULL
			EXCEPT
			SELECT DISTINCT \"dc.contributor.advisor1.fl_str_mv\" as name FROM bdtd_db WHERE \"dc.contributor.advisor1.fl_str_mv\" IS NOT NULL
			");
		pg_query("
			INSERT INTO researcher (name)
			SELECT DISTINCT \"dc.contributor.advisor1.fl_str_mv\" as name FROM bdtd_db WHERE \"dc.contributor.advisor1.fl_str_mv\" IS NOT NULL
			");

		// INSERT INTO relationship
		

		pg_query("
			INSERT INTO relationship (id_advisor, id_author, type, year, url, citation, topic, author_lattes, institution_acron, institution_name, title, abstract, url_bdtd)
			SELECT adv.id, aut.id, db.format, db.\"publishDate\", db.url, db.\"dc.identifier.citation.fl_str_mv\", db.topic, db.\"dc.contributor.authorLattes.fl_str_mv\", db.network_acronym_str, db.network_name_str, db.title, db.description, db.url_bdtd
			FROM researcher adv, researcher aut, bdtd_db db 
			WHERE aut.name = author
			and adv.name = \"dc.contributor.advisor1.fl_str_mv\"
			");

	
		// CREATING COLUMN AND INDICES FOR FULL TEXT SEARCH


		pg_query("
			ALTER TABLE researcher ADD COLUMN IF NOT EXISTS text_vector TSVECTOR;
			UPDATE researcher SET text_vector = to_tsvector(name);
			CREATE INDEX IF NOT EXISTS idx_text_vector_researcher ON researcher USING gin (text_vector);

			ALTER TABLE relationship ADD COLUMN IF NOT EXISTS text_vector TSVECTOR;
			UPDATE relationship SET text_vector = to_tsvector(title||' '||topic);
			CREATE INDEX IF NOT EXISTS idx_text_vector_relationship ON relationship USING gin (text_vector);
			");

		pg_close($dbconn);
		set_time_limit(120);
		
		echo "END";

?>
