<?php
	set_time_limit(1000000);

	$dbconn = pg_connect("host=localhost dbname=projeto user=postgres password=postgres") or die('Could notconnect: ' . pg_last_error());

	pg_query("DROP TABLE IF EXISTS ocebio_aux_table; DROP SEQUENCE IF EXISTS ocebio_aux_table_id_seq;");

	pg_query("CREATE SEQUENCE IF NOT EXISTS ocebio_aux_table_id_seq;");

	pg_query("CREATE TABLE public.ocebio_aux_table
				(
				    local character varying COLLATE pg_catalog.\"default\",
				    id integer NOT NULL DEFAULT nextval('ocebio_aux_table_id_seq'::regclass),
				    tipo character varying COLLATE pg_catalog.\"default\",
				    titulo character varying COLLATE pg_catalog.\"default\",
				    autor character varying COLLATE pg_catalog.\"default\",
				    ano character varying COLLATE pg_catalog.\"default\",
				    notasg character varying COLLATE pg_catalog.\"default\",
				    CONSTRAINT ocebio_aux_table_pkey PRIMARY KEY (id)
				)
				WITH (
				    OIDS = FALSE
				)
				TABLESPACE pg_default;

				ALTER TABLE public.ocebio_aux_table
				    OWNER to postgres;");

	pg_query("
			COPY ocebio_aux_table (local, tipo, titulo, autor, ano, notasg)
			FROM 'C:\\Program Files\\PostgreSQL\\10\\oceanografia_biologica.csv' DELIMITER ',' CSV HEADER;
			");
			///home/gabrielm/Downloads/oceanografia_biologica.csv
			//C:\\Program Files\\PostgreSQL\\10\\oceanografia_biologica.csv
	pg_query("
				UPDATE ocebio_aux_table
				SET tipo = 'm'
				WHERE tipo = 'Dissertacao'
				");
	pg_query("
				UPDATE ocebio_aux_table
				SET tipo = 'd'
				WHERE tipo = 'Tese'
				");


	$temp_result = pg_query(
			"SELECT id, notasg FROM ocebio_aux_table WHERE notasg LIKE '%Orientador:%'"
			);
		$numrows = pg_num_rows($temp_result);
	for ($i = 0; $i < $numrows; $i++) {
		$line_data = pg_fetch_row($temp_result, $i);
		$aux = str_replace("Prof. ","",$line_data[1]);
		$aux = str_replace("Dr. ","",$aux);
		$aux = str_replace("Profa. ","",$aux);
		$aux = str_replace("Dra. ","",$aux);
		$aux = str_replace("Orientador: ","",$aux);
		$aux = str_replace("Orientadora: ","",$aux);
		$aux = str_replace("Lic. ","",$aux);
		$aux = str_replace("_x000D_","",$aux);
		$aux = str_replace("'","",$aux);
		$aux = explode('.',$aux);
		$out = "";
		for ($ii = 0; $ii < (count($aux)-1); $ii++) {
			$out = $out.$aux[$ii];
		}
		if (strpos($out, '<br />') == true){
			$out = explode("<br />",$out);
			$out = $out[0];
		}
		if (strpos($out, '<br />') == true){
			$out = explode("\\n",$out);
			$out = $out[0];
		}

		$insert_line_data = $out;
		pg_query("UPDATE ocebio_aux_table
				SET notasg = '".$insert_line_data."'
				WHERE id = '".$line_data[0]."'");
	}




	pg_close($dbconn);
		set_time_limit(120);
?>