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
	file = fopen(argv[3], "w");

	//Mallocs room for the query
	char query[500];

	//If the database is not opened correctly, then error message
	if(rc) {
		fprintf(stderr, "Can’t open database: %s\n", sqlite3_errmsg(db));
		sqlite3_close(db);
		return(1);
	}
	else{ //If no error, then proceed

		//Concatenates the string together and grabs all rows from the table
		strcpy(query, "SELECT * FROM ");
		strcat(query, argv[2]);
		strcat(query, ";");
		if(sqlite3_prepare_v2(db, query, -1, &statement, NULL) == SQLITE_OK){

			//While the step is a row, write it to the csv file only if the type is an integer or text
			int i;
			while(sqlite3_step(statement) == SQLITE_ROW){

				for(i=0; i<sqlite3_column_count(statement)-1; i++){
					if(sqlite3_column_type(statement, i) == SQLITE_INTEGER){
						fprintf(file, "%d,", sqlite3_column_int(statement, i));
					}
					else if(sqlite3_column_type(statement, i) == SQLITE_TEXT){
						fprintf(file, "'%s',", sqlite3_column_text(statement, i));
					}
					else{
						sqlite3_finalize(statement);
						fprintf (stderr,"Incompatible data type\n");
						return (1);
					}
				}

				//When at the last column of each row, write the line to the csv and add a null terminator
				if(sqlite3_column_type(statement, i) == SQLITE_INTEGER){
					fprintf(file, "%d\n", sqlite3_column_int(statement, i));
				}
				else if(sqlite3_column_type(statement, i) == SQLITE_TEXT){
					fprintf(file, "'%s'\n", sqlite3_column_text(statement, i));
				}
				else{
					sqlite3_finalize(statement);
					fprintf (stderr,"Incompatible data type\n");
					return (1);
				}
			}
		}
	}

	sqlite3_finalize(statement);

	fclose(file);
	sqlite3_close(db);
	return 0;
}
