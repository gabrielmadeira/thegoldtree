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
			FROM '/home/gabrielm/Downloads/oceanografia_biologica.csv' DELIMITER ',' CSV HEADER;
			");

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
		$aux = explode(': ',$line_data[1]);
		$aux2 = explode('.', $aux[1]);
		$insert_line_data = $aux2[1];
		pg_query("UPDATE ocebio_aux_table
				SET notasg = '".$insert_line_data."'
				WHERE id = '".$line_data[0]."'");
	}




	pg_close($dbconn);
		set_time_limit(120);
?>