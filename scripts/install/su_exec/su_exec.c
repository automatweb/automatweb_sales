#include <stdio.h>
#include <signal.h>
#include <sys/types.h>
#include <unistd.h>

#define ERR_NOFILE -1
#define ERR_NOKEY -2 

typedef struct _command
{
	char *real_cmd;
} COMMAND;

typedef struct _cmdfile 
{
	COMMAND *cmds;
	int num_cmds;
	char *orig_key;
	char *crypt_key;
} CMD_FILE;

typedef struct _trans {
	char *from;
	char *to;
} CMD_TRANS_ENTRY;

CMD_TRANS_ENTRY g_cmd_trans_tbl[] = {
	{"print", "echo" },
	{"copy", "cp"},
	{"mkdir", "mkdir"},
	{"chmod", "chmod"},
	{"chown", "chown"},
	{"ln", "ln"},
	{"rndc","rndc"},
	{"find", "find"},
	{"rm", "rm"},
	{"move", "mv"},
};

char *g_err_line;

int open_and_parse_cmd_file(char *file, CMD_FILE *cf)
{
	FILE *fp;
	char line[10000], t_cmd[10000];
	int num = 0, c_num, i, found, lineno;
	
	if (!(fp = fopen(file, "r")))
	{
		return ERR_NOFILE;
	}

	// fisrt line of file contains number of parsable commands
	cf->num_cmds = atoi(fgets(line,10000,fp));
	
	// alloc mem for commands
	cf->cmds = (COMMAND *)malloc(sizeof(COMMAND)*cf->num_cmds);

	while (!feof(fp))
	{
		fgets(line,10000,fp);
		if (feof(fp))
		{
			return 0;
		}
		// remove the \n from the read line
		line[strlen(line)-1] = 0;
		lineno++;

		// check if it is a key line
		if (strstr(line, "Orig_key: "))
		{
			cf->orig_key = (char *)strdup(line+strlen("Orig_key: "));
		}
		else
		if (strstr(line,"Crypt_key: "))
		{
			cf->crypt_key = (char *)strdup(line+strlen("Crypt_key: "));
		}
		else
		{
			if (num >= cf->num_cmds)
			{
				continue;
			}

			// command line
			// find the real command from the trans_tbl
			// get the first word from the line
			memset(t_cmd, 0, 10000);
			c_num = 0;
			while(!(line[c_num] == ' ' || line[c_num] == '0'))
			{
				t_cmd[c_num] = line[c_num];
				c_num++;
			}

//			printf("got command line = %s , c_num = %i \n", line, c_num);

			for (i = 0, found = 0; (i < (sizeof(g_cmd_trans_tbl) / sizeof(CMD_TRANS_ENTRY))) && found == 0; i++)
			{
//				printf("try to match %s with %s \n", t_cmd, g_cmd_trans_tbl[i].to);
				if (strcmp(t_cmd, g_cmd_trans_tbl[i].from) == 0)
				{
//					printf("found trans cmd for %s eq %s\n", t_cmd, g_cmd_trans_tbl[i].to);
					strcpy(t_cmd, g_cmd_trans_tbl[i].to);
					found = 1;
				}
			}
			if (!found)
			{
				g_err_line = (char *)strdup(line);
				return lineno;
			}

			// store it in cmd list
			cf->cmds[num].real_cmd = (char *)malloc(sizeof(t_cmd)+sizeof(line)+1);
			strcpy(cf->cmds[num].real_cmd, t_cmd);
			strcpy(cf->cmds[num].real_cmd+(strlen(cf->cmds[num].real_cmd)), line+c_num);

//			printf("got command %s \n", cf->cmds[num].real_cmd);
			num++;
		}
	}

	fclose(fp);
	return 0;
}

int free_cmds(CMD_FILE *cf)
{
	int i;
	for (i = 0; i < cf->num_cmds; i++)
	{
		free(cf->cmds[i].real_cmd);
	}
	free(cf->orig_key);
	free(cf->crypt_key);
	free(cf->cmds);
}

int check_keys(CMD_FILE *cf)
{
	int o_key, c_key, calc;

	o_key = atoi(cf->orig_key);
	c_key = atoi(cf->crypt_key);

	// something dumb and simple
	calc = ((o_key * 2) + 13) / 2;

	if (c_key != calc)
	{
		return 0;
	}
	return 1;
}

int main(int argc, char **argv)
{
	int errline = 0, i;
	CMD_FILE cf;

	// check if cmd file was given
	if (argc < 2)
	{
		printf("USAGE: su_exec cmdfile\n");
		return 0;
	}

	if ((errline = open_and_parse_cmd_file(argv[1], &cf)) != 0)
	{
		if (errline == ERR_NOFILE)
		{
			printf("could not find command file %s \n", argv[1]);
			return ERR_NOFILE;
		}
		if (errline == ERR_NOKEY)
		{
			printf("authentication keys do not match! will not process file \n");
			return ERR_NOKEY;
		}

		printf("error on line %i of command file: %s\n", errline, g_err_line);
		free(g_err_line);
		free_cmds(&cf);
		return 0;
	}

	if (!check_keys(&cf))
	{
		printf("keys do not match!\n");
		free_cmds(&cf);
		return 0;
	}

	// set the uid to root, so that shell commands think they are really run from the root account
	setuid(geteuid());

	for (i = 0; i < cf.num_cmds; i++)
	{
		printf("exec command %s <br>\n", cf.cmds[i].real_cmd);
		system(cf.cmds[i].real_cmd);
	}
	free_cmds(&cf);
	return 0;
}
