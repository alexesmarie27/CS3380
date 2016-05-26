#include <stdio.h>
#include <sqlite3.h>
#include <stdlib.h>
#include <string.h>

int main(int argc, char** argv)
{
	sqlite3* db;

	if(argc != 4)
	{
	  fprintf(stderr, "USAGE: %s <database file> <table name> <CSV file>\n", argv[0]);
	  return 1;
	}

	//Opens the database
	int rc = sqlite3_open(argv[1], &db);
	sqlite3_stmt* statement;

	//Opens up the csv file
	FILE* file;
	file = fopen(argv[3], "r");

	//Mallocs room for the query
	char query[500];

	//If the database is not opened correctly, then error message
	if(rc) {
		fprintf(stderr, "Can’t open database: %s\n", sqlite3_errmsg(db));
		sqlite3_close(db);
		return(1);
	}
	else{ //if the database was opened correctly, then proceed

		//Concatenate the string together, and delete all rows from the database table
		strcpy(query, "DELETE FROM ");
		strcat(query, argv[2]);
		strcat(query, ";");
		if(sqlite3_prepare_v2(db, query, -1, &statement, NULL) == SQLITE_OK){
			while(sqlite3_step(statement) == SQLITE_ROW){}

			sqlite3_finalize(statement);
		}
		else{
			sqlite3_finalize(statement);
			fprintf (stderr,"Error with SQLite statement\n");
			return (1);
		}

		//grab a line in the csv file and insert it into the table
		char line[500];
		do{
			fgets(line, 500, file);

			strcpy(query, "INSERT INTO ");
			strcat(query, argv[2]);
			strcat(query, " VALUES ");
			strcat(query, "(");
			strcat(query, line);
			strcat(query, ");");
			if(sqlite3_prepare_v2(db, query, -1, &statement, NULL) == SQLITE_OK){
				while(sqlite3_step(statement) == SQLITE_ROW){}

				sqlite3_finalize(statement);
			}
		}while(!feof(file));
	}

	fclose(file);
	sqlite3_close(db);
	return 0;
}
