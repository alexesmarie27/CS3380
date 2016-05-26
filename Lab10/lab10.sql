DROP SCHEMA IF EXISTS lab10 CASCADE;
CREATE SCHEMA lab10;
SET search_path = lab10;

CREATE TABLE group_standings (
	team character varying(25) PRIMARY KEY,
	wins smallint NOT NULL CHECK (wins >= 0),
	losses smallint NOT NULL CHECK(losses >= 0),
	draws smallint NOT NULL CHECK(draws >= 0),
	points smallint NOT NULL CHECK (points >= 0)
);

\copy group_standings FROM '/facstaff/klaricm/public_cs3380/lab10/lab10_data.csv' DELIMITER ',' CSV HEADER;

CREATE OR REPLACE FUNCTION calc_points_total(w integer, d integer)
	RETURNS integer AS $$
	SELECT (3 * $1) + $2 AS result;
	$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION update_points_total() RETURNS trigger AS $$
	BEGIN
		NEW.points := calc_points_total(NEW.wins,NEW.draws);
		RETURN NEW;
	END;
	$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_update_points_total BEFORE INSERT OR UPDATE ON group_standings FOR EACH ROW
	EXECUTE PROCEDURE update_points_total();

CREATE OR REPLACE FUNCTION disallow_team_name_update() RETURNS trigger AS $$
	BEGIN
		IF NEW.team != OLD.team THEN
			RAISE EXCEPTION 'Changing the team name is not allowed';
		END IF;
		RETURN OLD;
	END;
	$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_disallow_team_name_update BEFORE UPDATE OF team ON group_standings FOR EACH ROW
	EXECUTE PROCEDURE disallow_team_name_update();

ALTER TABLE group_standings ADD COLUMN rank SMALLINT;

CREATE OR REPLACE FUNCTION update_rank() RETURNS trigger AS $$
	BEGIN
		UPDATE group_standings SET rank = r.rank FROM(
			SELECT team, rank() OVER (ORDER BY points DESC) FROM group_standings) AS r
			WHERE group_standings.team = r.team;
		RETURN NEW;
	END;
	$$ LANGUAGE plpgsql;

CREATE TRIGGER tr_update_rank AFTER INSERT OR UPDATE OF wins, draws ON group_standings FOR EACH STATEMENT
	EXECUTE PROCEDURE update_rank();
