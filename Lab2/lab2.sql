DROP SCHEMA IF EXISTS lab2 CASCADE;
CREATE SCHEMA lab2;
SET search_path = lab2;

CREATE TABLE building (
	name varchar(50),
	city varchar(50),
	address varchar(100),
	state char(2),
	zipcode char(5),
	PRIMARY KEY(address, zipcode)
);

INSERT INTO building VALUES	(
	'Health Center',
	'Columbia',
	'123 Main St',
	'MO',
	'65201'
);

INSERT INTO building VALUES	(
	'Medical Department',
	'Columbia',
	'55876 East Broadway St',
	'MO',
	'65201'
);

INSERT INTO building VALUES	(
	'Emergency Clinic',
	'Columbia',
	'1127 Providence Rd',
	'MO',
	'65201'
);

CREATE TABLE office (
	room_number varchar(6) PRIMARY KEY,
	waiting_room_capacity integer,
	address varchar(100),
	zipcode char(5),
	FOREIGN KEY(address, zipcode) REFERENCES building ON DELETE CASCADE
);

INSERT INTO office VALUES	(
	'305C',
	'85',
	'1127 Providence Rd',
	'65201'
);

INSERT INTO office VALUES	(
	'876',
	'76',
	'55876 East Broadway St',
	'65201'
);

INSERT INTO office VALUES	(
	'124A',
	'50',
	'123 Main St',
	'65201'
);

CREATE TABLE doctor (
	medical_license_num varchar(20) PRIMARY KEY,
	first_name varchar(25),
	last_name varchar(25),
	office_num varchar(6),
	FOREIGN KEY(office_num) REFERENCES office (room_number) ON DELETE CASCADE
);

INSERT INTO doctor VALUES	(
	'1175899',
	'Alex',
	'Smith',
	'124A'
);

INSERT INTO doctor VALUES	(
	'878795221',
	'Reed',
	'Michaels',
	'876'
);

INSERT INTO doctor VALUES	(
	'55694127',
	'Sarah',
	'Mitchell',
	'305C'
);

CREATE TABLE patient (
	ssn char(9) PRIMARY KEY,
	first_name varchar(25),
	last_name varchar(25)
);

INSERT INTO patient VALUES	(
	'965412375',
	'Alexes',
	'Presswood'
);

INSERT INTO patient VALUES	(
	'741221589',
	'Tyler',
	'Brown'
);

INSERT INTO patient VALUES (
	'636652147',
	'AJ',
	'Stegall'
);

CREATE TABLE doctor_appointments (
	appt_time time,
	appt_date date,
	doctor_license_num varchar(20),
	patient_ssn char(9),
	FOREIGN KEY(doctor_license_num) REFERENCES doctor(medical_license_num) ON DELETE CASCADE,
	FOREIGN KEY(patient_ssn) REFERENCES patient(ssn) ON DELETE CASCADE
);

INSERT INTO doctor_appointments VALUES (
	'12:00:00',
	'01/27/2015',
	'55694127',
	'636652147'
);

INSERT INTO doctor_appointments VALUES (
	'04:00:00',
	'02/28/2015',
	'878795221',
	'741221589'
);

INSERT INTO doctor_appointments VALUES (
	'05:30:00',
	'03/05/2015',
	'1175899',
	'965412375'
);

CREATE TABLE insurance (
	policy_num varchar(20),
	insurer varchar(50),
	patient_ssn char(9),
	FOREIGN KEY(patient_ssn) REFERENCES patient(ssn) ON DELETE CASCADE
);

INSERT INTO insurance VALUES (
	'44856-A12',
	'Blue Cross Insurance',
	'636652147'
);

INSERT INTO insurance VALUES (
	'498652-A178',
	'Blue Cross Insurance',
	'741221589'
);

INSERT INTO insurance VALUES (
	'123AC457C2',
	'Shelter Insurance',
	'965412375'
);

CREATE TABLE labwork (
	test_name varchar(50),
	test_value varchar(8),
	test_timestamp timestamp,
	patient_ssn char(9),
	FOREIGN KEY(patient_ssn) REFERENCES patient(ssn) ON DELETE CASCADE,
	PRIMARY KEY(test_name, test_timestamp)
);

INSERT INTO labwork VALUES (
	'Ankle X-Ray',
	'negative',
	'2014-01-08 04:05:06',
	'965412375'
);

INSERT INTO labwork VALUES (
	'Leg X-Ray',
	'positive',
	'2015-08-08 05:06:07',
	'741221589'
);

INSERT INTO labwork VALUES (
	'Sinus X-Ray',
	'positive',
	'2015-05-07 08:09:05',
	'636652147'
);

CREATE TABLE condition (
	icd10 varchar(9) PRIMARY KEY,
	description varchar(75)
);

INSERT INTO condition VALUES (
	'S93.409',
	'Sprain of ankle to unspecified ankle'
);

INSERT INTO condition VALUES (
	'J01.1',
	'Acute frontal sinusitis'
);

INSERT INTO condition VALUES (
	'S82.0',
	'Fracture of patella'
);

CREATE TABLE patient_conditions(
	patient_ssn char(9),
	FOREIGN KEY(patient_ssn) REFERENCES patient(ssn) ON DELETE CASCADE,
	icd10 varchar(8),
	FOREIGN KEY(icd10) REFERENCES condition ON DELETE CASCADE
);

INSERT INTO patient_conditions VALUES (
	'965412375',
	'S93.409'
);

INSERT INTO patient_conditions VALUES (
	'636652147',
	'J01.1'
);

INSERT INTO patient_conditions VALUES (
	'741221589',
	'S82.0'
);
