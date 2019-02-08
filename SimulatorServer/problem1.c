#include <stdio.h>
#include <string.h>
#define MAX 2048

int main(void){
	FILE *fp;
	char *fname = "/home/ubuntu/input.txt";
	char readline[MAX];
	char *token;
	char parameter1[MAX];
	char parameter2[MAX];
	int i,j,ret,count;
	char token_key[] = " -.";


	fp = fopen( fname, "r" );
	if( fp == NULL ){
		printf( "%sファイルが開けません\n", fname );
		return -1;
	}

	//input.txtファイルを読み込む
	while( fgets(readline,MAX,fp) != NULL ){
		if(strncmp( readline, "#", 1 )){

			//パラメータセットをパース
			token = strtok( readline, token_key );
			if(strcmp(token,"parameter1")==0){
				token = strtok( NULL, token_key );
				strcpy(parameter1,token);
			}else if(strcmp(token,"parameter2")==0){
				token = strtok( NULL, token_key );
				strcpy(parameter2,token);

			}

		}
	}

	fclose( fp );

	//評価値を計算する
	parameter1[strlen(parameter1)-1] = '\0';
	parameter2[strlen(parameter2)-1] = '\0';

	count=0;
	for(i=0; i<strlen(parameter1); i++){
		if(parameter1[i] == '1') count++;
	}
	for(i=0; i<strlen(parameter2); i++){
		if(parameter2[i] == '0') count++;
	}

	//出力
	printf("fitness_value %d",count);
}