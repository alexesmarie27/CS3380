/*
	1. ID is the primary key of the table, so that creates an automatic index on that attribute.
	Primary keys are automatically indexed.
*/

/*2*/
SELECT * FROM lab7.banks WHERE state = 'Missouri';
EXPLAIN ANALYZE SELECT * FROM lab7.banks WHERE state = 'Missouri';
/*
	Query Plan:
	Seq Scan on banks  (cost=0.00..894.98 rows=996 width=124) (actual time=1.224..51.098 rows=996 loops=1)
    Filter: ((state)::text ~~* 'Missouri'::text)
    Total runtime: 52.191 ms
*/
CREATE INDEX ON lab7.banks (state);
EXPLAIN ANALYZE SELECT * FROM lab7.banks WHERE state = 'Missouri';
/*
	Query Plan
    Bitmap Heap Scan on banks  (cost=23.97..598.42 rows=996 width=124) (actual time=0.495..5.048 rows=996 loops=1)
    Recheck Cond: ((state)::text = 'Missouri'::text)
    ->  Bitmap Index Scan on banks_state_idx2  (cost=0.00..23.72 rows=996 width=0) (actual time=0.366..0.366 rows=996 loops=1)
    Index Cond: ((state)::text = 'Missouri'::text)
    Total runtime: 6.065 ms

	Speed up:
		46.126 ms faster
		88% faster
*/

/*3*/
EXPLAIN ANALYZE SELECT * FROM lab7.banks ORDER BY name;
/*
	Query Plan
	Sort  (cost=4657.15..4726.14 rows=27598 width=124) (actual time=306.419..444.059 rows=27598 loops=1)
    Sort Key: name
    Sort Method: external merge  Disk: 3760kB
    ->  Seq Scan on banks  (cost=0.00..825.98 rows=27598 width=124) (actual time=0.013..34.965 rows=27598 loops=1)
    Total runtime: 473.534 ms
*/
CREATE INDEX ON lab7.banks (name);
EXPLAIN ANALYZE SELECT * FROM lab7.banks ORDER BY name;
/*
	Query Plan
	Index Scan using banks_name_idx on banks  (cost=0.00..3294.27 rows=27598 width=124) (actual time=0.051..50.188 rows=27598 loops=1)
    Total runtime: 77.672 ms

	Speed up:
		395.862 ms faster
		84% faster
*/

/*4*/
CREATE INDEX ON lab7.banks (is_active);

/*5*/
EXPLAIN ANALYZE SELECT * FROM banks WHERE is_active = TRUE;
EXPLAIN ANALYZE SELECT * FROM banks WHERE is_active = FALSE;
/*
	Which one uses an index?
		is_active = true
	Why?
		it is faster for the program to not use the index, so the program decides to ignore the index and run a sequential scan
*/

/*6*/
SELECT * FROM lab7.banks WHERE insured >= '2000-01-01';
EXPLAIN ANALYZE SELECT * FROM lab7.banks WHERE insured >= '2000-01-01';
/*
	Query Plan
	Seq Scan on banks  (cost=0.00..894.98 rows=1450 width=124) (actual time=2.814..11.091 rows=1451 loops=1)
    Filter: (insured >= '2000-01-01'::date)
    Total runtime: 12.554 ms
*/
CREATE INDEX ON lab7.banks (insured) WHERE NOT insured = '1934-01-01';
SELECT * FROM lab7.banks WHERE insured >= '2000-01-01';
EXPLAIN ANALYZE SELECT * FROM lab7.banks WHERE insured >= '2000-01-01';
/*
	Query Plan
	Index Scan using banks_insured_idx on banks  (cost=0.00..573.89 rows=1450 width=124) (actual time=0.042..2.272 rows=1451 loops=1)
    Index Cond: (insured >= '2000-01-01'::date)
    Total runtime: 3.738 ms

	Speed up:
		8.816 ms faster
		70% faster
*/

/*7*/
SELECT id, name, city, state, assets, deposits FROM lab7.banks WHERE (asset/deposits) < 0.5 AND deposits > 0;
EXPLAIN ANALYZE SELECT id, name, city, state, assets, deposits FROM lab7.banks WHERE (assets/deposits) < 0.5 AND deposits > 0;
/*
	Query Plan
	Seq Scan on banks  (cost=0.00..1032.97 rows=8531 width=63) (actual time=31.543..42.433 rows=46 loops=1)
    Filter: ((deposits > 0::numeric) AND ((assets / deposits) < 0.5))
    Total runtime: 42.515 ms
*/
CREATE INDEX ON lab7.banks ((assets/deposits)) WHERE NOT deposits = 0;
EXPLAIN ANALYZE SELECT id, name, city, state, assets, deposits FROM lab7.banks WHERE (assets/deposits) < 0.5 AND deposits > 0;
/*
	Query Plan
	Bitmap Heap Scan on banks  (cost=215.38..925.79 rows=8531 width=63) (actual time=0.078..0.371 rows=46 loops=1)
    Recheck Cond: (((assets / deposits) < 0.5) AND (deposits <> 0::numeric))
    Filter: (deposits > 0::numeric)
    ->  Bitmap Index Scan on banks_expr_idx  (cost=0.00..213.25 rows=9166 width=0) (actual time=0.053..0.053 rows=46 loops=1)
    Index Cond: ((assets / deposits) < 0.5)
    Total runtime: 0.453 ms

	Speed up:
		42.062 ms faster
		99% faster
*/
