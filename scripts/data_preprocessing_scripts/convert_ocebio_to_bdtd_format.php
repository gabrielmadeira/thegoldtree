<?php
	set_time_limit(1000000);

	$dbconn = pg_connect("host=localhost dbname=projeto user=postgres password=postgres") or die('Could notconnect: ' . pg_last_error());

	pg_query("DROP TABLE IF EXISTS convertion_aux_table;");

	pg_query("
			CREATE TABLE public.convertion_aux_table
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
			    CONSTRAINT convertion_aux_table_pkey PRIMARY KEY (id)
			)
			WITH (
			    OIDS = FALSE
			)
			TABLESPACE pg_default;

			ALTER TABLE public.convertion_aux_table
			    OWNER to postgres;
			");


	pg_query("
			INSERT INTO convertion_aux_table (format, title, author, data, \"dc.contributor.advisor1.fl_str_mv\", network_acronym_str, network_name_str, id)
			SELECT tipo, titulo, autor, ano, notasg, 'FURG', 'Repositório Institucional da FURG', 'convertion_aux_table' || id
			FROM ocebio_aux_table
			");

	pg_close($dbconn);
	set_time_limit(120);
?>