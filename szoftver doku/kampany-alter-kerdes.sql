/*ALterek kész vannak 2017-04-23*/

ALTER TABLE lmp_tny_kampany
  ADD kerdes VARCHAR(255),
  ADD valaszok TEXT,
  ADD kerdestipus TINYINT;
  
ALTER TABLE lmp_tny_kampany
  ADD kerdes1 VARCHAR(255),
  ADD valaszok1 TEXT,
  ADD kerdestipus1 TINYINT,
  ADD kerdes2 VARCHAR(255),
  ADD valaszok2 TEXT,
  ADD kerdestipus2 TINYINT,
  ADD kerdes3 VARCHAR(255),
  ADD valaszok3 TEXT,
  ADD kerdestipus3 TINYINT,
  ADD kerdes4 VARCHAR(255),
  ADD valaszok4 TEXT,
  ADD kerdestipus4 TINYINT,
  ADD hirlevel_id int(11);
  
ALTER TABLE lmp_tny_kampany_kapcs
  ADD valasz1 varchar(255),
  ADD valasz2 varchar(255),
  ADD valasz3 varchar(255),
  ADD valasz4 varchar(255);

  
  
  