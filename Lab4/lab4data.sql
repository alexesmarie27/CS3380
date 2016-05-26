-- The double dashes are comments within .sql files


-- Drop, create and set schema for this example
DROP SCHEMA IF EXISTS lab4 CASCADE;


CREATE SCHEMA lab4;
SET search_path = lab4;

CREATE TABLE university (
    uid serial PRIMARY KEY,
    university_name varchar(50),
    city varchar(50)
);


CREATE TABLE person (
    pid serial PRIMARY KEY,
    uid integer REFERENCES university ON DELETE CASCADE,
    fname varchar(25) NOT NULL,
    lname varchar(25) NOT NULL
);

CREATE TABLE activity (
    activity_name varchar(50) PRIMARY KEY
); 

CREATE TABLE participated_in (
    pid integer REFERENCES person,
    activity_name varchar(50) REFERENCES activity,
    activity_date date,
    activity_duration integer,
    PRIMARY KEY(pid, activity_name, activity_date)
);

CREATE TABLE body_composition (
    pid integer PRIMARY KEY REFERENCES person ON DELETE CASCADE, 
    height integer NOT NULL,
    weight integer NOT NULL,
    age integer NOT NULL
);



INSERT INTO university VALUES (DEFAULT, 'University of Cincinnati', 'Cincinnati');
INSERT INTO university VALUES (DEFAULT, 'University of Missouri Columbia', 'Columbia');
INSERT INTO university VALUES (DEFAULT, 'Stanford University
', 'Stanford');
INSERT INTO university VALUES (DEFAULT, 'Yale University
', 'New Haven');
INSERT INTO university VALUES (DEFAULT, 'Ohio State University', 'Columbus');
INSERT INTO university VALUES (DEFAULT, 'University of Alabama', 'Tuscaloosa');
INSERT INTO university VALUES (DEFAULT, 'Missouri University of Science and Technology', 'Rolla');
INSERT INTO university VALUES (DEFAULT, 'University of Georgia', 'Athens');
INSERT INTO university VALUES (DEFAULT, 'Columbia College', 'Columbia');


INSERT INTO person VALUES (DEFAULT, 2, 'Bob', 'Smith');
INSERT INTO person VALUES (DEFAULT, 3, 'Aaron', 'Gates');
INSERT INTO person VALUES (DEFAULT, 1, 'Justin', 'Long');
INSERT INTO person VALUES (DEFAULT, 5, 'Steve', 'Rogers');
INSERT INTO person VALUES (DEFAULT, 4, 'Alan', 'Belshore');
INSERT INTO person VALUES (DEFAULT, 8, 'Fred', 'Bier');
INSERT INTO person VALUES (DEFAULT, 9, 'Andrew', 'Watson');
INSERT INTO person VALUES (DEFAULT, 9, 'Adam', 'Shore');
INSERT INTO person VALUES (DEFAULT, 2, 'Alan', 'Lewis');
INSERT INTO person VALUES (DEFAULT, 5, 'Bette', 'Lueking');
INSERT INTO person VALUES (DEFAULT, 4, 'Kent', 'Jobs');
INSERT INTO person VALUES (DEFAULT, 3, 'James', 'Ives');



INSERT INTO activity VALUES ('weightlifting');
INSERT INTO activity VALUES ('running');
INSERT INTO activity VALUES ('racquetball');
INSERT INTO activity VALUES ('baseball');
INSERT INTO activity VALUES ('biking');
INSERT INTO activity VALUES ('soccer');
INSERT INTO activity VALUES ('handball');
INSERT INTO activity VALUES ('tennis');



INSERT INTO participated_in VALUES 
(1, 'biking', '2014-04-13', 43),
(1, 'soccer', '2014-04-17', 77),
(2, 'weightlifting', '2014-04-27', 33),
(2, 'soccer', '2014-05-01', 41),
(3, 'weightlifting', '2014-05-06', 58),
(3, 'running', '2014-05-09', 41),
(4, 'biking', '2014-05-15', 131),
(4, 'soccer', '2014-05-21', 60),
(5, 'baseball', '2014-06-01', 95),
(6, 'racquetball', '2014-06-04', 48),
(6, 'soccer', '2014-06-07', 81),
(7, 'weightlifting', '2014-06-11', 71),
(7, 'soccer', '2014-06-17', 47),
(8, 'racquetball', '2014-06-23', 33),
(8, 'running', '2014-06-27', 24),
(9, 'soccer', '2014-07-02', 56),
(10, 'running', '2014-07-05', 78),
(11, 'racquetball', '2014-07-08', 61),
(12, 'biking', '2014-07-11', 123)
;



INSERT INTO body_composition VALUES (1, 71, 130, 22);
INSERT INTO body_composition VALUES (2, 68, 170, 19);
INSERT INTO body_composition VALUES (3, 85, 230, 21);
INSERT INTO body_composition VALUES (4, 60, 120, 33);
INSERT INTO body_composition VALUES (5, 80, 233, 31);
INSERT INTO body_composition VALUES (6, 70, 177, 16);
INSERT INTO body_composition VALUES (7, 71, 155, 20);
INSERT INTO body_composition VALUES (8, 75, 250, 51);
INSERT INTO body_composition VALUES (9, 76, 225, 65);
INSERT INTO body_composition VALUES (10, 65, 175, 45);
INSERT INTO body_composition VALUES (11, 60, 125, 67);
INSERT INTO body_composition VALUES (12, 55, 120, 42);
