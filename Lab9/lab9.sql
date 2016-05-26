--1--
SELECT name10 FROM tl_2010_us_state10 WHERE ST_Intersects(coords,
	ST_SetSRID(ST_MakeBox2D(ST_Point(-110, 35),ST_Point(-109, 36)), 4326)) ORDER BY name10 DESC;

--2--
SELECT b.stusps10, b.name10 FROM tl_2010_us_state10 AS a INNER JOIN tl_2010_us_state10 AS b
	ON ST_Touches(a.coords, b.coords) WHERE a.stusps10 = 'NC' ORDER BY b.name10;

--3--
SELECT a.name10 FROM tl_2010_us_uac10 AS a INNER JOIN tl_2010_us_state10 AS b
	ON ST_Within(a.coords, b.coords) WHERE b.stusps10 = 'CO' ORDER BY a.name10;

--4--
SELECT a.name10, ((a.aland10 + a.awater10)/1000000) AS area FROM tl_2010_us_uac10 AS a INNER JOIN tl_2010_us_state10 AS b
	ON ST_Overlaps(a.coords, b.coords) WHERE b.stusps10 = 'PA' ORDER BY area DESC;

--5--
SELECT a.name10, b.name10 FROM tl_2010_us_uac10 AS a INNER JOIN tl_2010_us_uac10 AS b
	ON ST_Intersects(a.coords, b.coords) WHERE (a.name10 >= b.name10) AND NOT a.name10 = b.name10;

--6--
SELECT a.name10, COUNT(*) AS count FROM tl_2010_us_uac10 AS a INNER JOIN tl_2010_us_state10 AS b
	ON ST_Intersects(a.coords, b.coords) WHERE ((a.aland10 + a.awater10)/1000000) > 1500 GROUP BY a.name10
	HAVING COUNT(*) > 1 ORDER BY count DESC, a.name10;
