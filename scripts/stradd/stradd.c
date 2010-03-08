
#include <my_global.h>
#include <my_sys.h>
#include <mysql.h>
#include <m_ctype.h>

char *stradd(UDF_INIT *initid, UDF_ARGS *args,char *result, unsigned long *length,char *is_null, char *error);
my_bool stradd_init(UDF_INIT *initid, UDF_ARGS *args, char *message);
void stradd_deinit(UDF_INIT *initid);
void stradd_reset(UDF_INIT *initid, UDF_ARGS *args,char *is_null, char *error);
void stradd_add(UDF_INIT *initid, UDF_ARGS *args,char *is_null, char *error);

#define MAX_ENTRIES 1000

typedef struct _entry {
	int len;
	char *str;
} entry;

struct stradd_struct
{
	char *sep;
	int sep_len;
	char *collect;
	int collect_len;
	int is_uniq;
	int num_entries;
	entry entries[MAX_ENTRIES];
};

//#define DBG 1

my_bool stradd_init(UDF_INIT *initid, UDF_ARGS *args, char *message)
{
	struct stradd_struct *pt;
	long long iv;
	int i;
#ifdef DBG
	FILE *fp;
#endif

	if (args->arg_count < 2)
	{
		strcpy(message,"stradd() requires two arguments");
		return 1;
	}

	if (args->arg_count == 3 && args->arg_type[2] != INT_RESULT)
	{
		strcpy(message, "second argument must be number (1 =only unique, 0 = all)");
		return 1;
	}

	args->arg_type[0] = STRING_RESULT;
	args->arg_type[1] = STRING_RESULT;

	initid->maybe_null = 0;
	initid->max_length = 65535;
	
	initid->ptr = (char *)malloc(sizeof(struct stradd_struct));
	pt = (struct stradd_struct *)initid->ptr;
	
#ifdef DBG
	fp = fopen("/tmp/stradd.log","a");
	fprintf(fp, "init inited pt to %i \n",pt);
	fclose(fp);
#endif
	
	pt->collect = (char *)malloc(65535);
	memset(pt->collect, 0, 65535);
	pt->collect_len = 0;

	pt->sep = (char *)malloc(args->lengths[0]);
	pt->sep_len = args->lengths[0];
	memcpy(pt->sep, args->args[0], pt->sep_len);

	if (args->arg_count == 3 && args->args[2] != NULL)
	{
		iv = *((long long *)args->args[2]);
		if (iv == 1)
		{
			pt->is_uniq = 1;
			pt->num_entries = 0;
			for (i = 0; i < MAX_ENTRIES; i++)
			{
				pt->entries[i].len = 0;
				pt->entries[i].str = NULL;
			}
		}
	}
	else
	{
		pt->is_uniq = 0;
	}
	return 0;
}

void stradd_deinit(UDF_INIT *initid)
{
	struct stradd_struct *pt = ((struct stradd_struct *)initid->ptr);
	int i;

#ifdef DBG
	FILE *fp;
	fp = fopen("/tmp/stradd.log","a");
	fprintf(fp, "deinit() \n");
	fclose(fp);
#endif 
	free(pt->collect);
	free(pt->sep);

	if (pt->is_uniq == 1)
	{
		for (i = 0; i < pt->num_entries; i++)
		{
			if (pt->entries[i].str != NULL)
			{
				free(pt->entries[i].str);
			}
		}
	}
	free(initid->ptr);
}

void stradd_reset(UDF_INIT *initid, UDF_ARGS *args,char *is_null, char *error)
{
	struct stradd_struct *pt = ((struct stradd_struct *)initid->ptr);
	int i;
#ifdef DBG
	FILE *fp;

	fp = fopen("/tmp/stradd.log","a");
	fprintf(fp, "stradd_reset() \n");
#endif
	memset(pt->collect,0,65535);
	pt->collect_len = 0;
#ifdef DBG
	fprintf(fp, "exit stradd_reset()\n");	
	fclose(fp);
#endif	
	if (pt->is_uniq == 1)
	{
		for (i = 0; i < pt->num_entries; i++)
		{
			if (pt->entries[i].str != NULL)
			{
				free(pt->entries[i].str);
			}
			pt->entries[i].len = 0;
		}
		pt->num_entries = 0;
	}
	stradd_add(initid,args,is_null, error);
}

void stradd_add(UDF_INIT *initid, UDF_ARGS *args,char *is_null, char *error)
{
	struct stradd_struct *pt = ((struct stradd_struct *)initid->ptr);
	int i, found ,add_str = 1;
#ifdef DBG
	FILE *fp;
	fp = fopen("/tmp/stradd.log","a");
	fprintf(fp, "enter stradd_add() argc = %i arg1len = %i , collectlen = %i, collect = '%s' collectrptr = %i pt = %i \n",
		args->arg_count,
		args->lengths[1],
		pt->collect_len,
		pt->collect,
		pt->collect,
		pt);
	fprintf(fp, "arg0 = %s(%x) arg0len = %i arg1 = %s (%x) arg1len = %i\n",
		args->args[0],
		args->args[0],
		args->lengths[0],
		args->args[1],
		args->args[1],
		args->lengths[1]
	);

	fflush(fp);

	if (args->arg_type[1] != STRING_RESULT)
	{
		fprintf(fp, "ERROR, arg1 is not string!\n");
	}
#endif
	
	if (pt->is_uniq == 1 && args->lengths[1] > 0 && args->args[1] != NULL)
	{
		found = 0;
		for (i = 0; i < pt->num_entries; i++)
		{
			if (pt->entries[i].len == args->lengths[1])
			{
				if (strncmp(pt->entries[i].str, args->args[1], pt->entries[i].len) == 0)
				{
					found = 1;
				}
			}
		}
		if (found == 0)
		{
			pt->entries[pt->num_entries].len = args->lengths[1];
			pt->entries[pt->num_entries].str = (char *)malloc(args->lengths[1]);
			memcpy(pt->entries[pt->num_entries].str, args->args[1], args->lengths[1]);
			pt->num_entries++;
		}
		else
		{
			add_str = 0;
		}
	}

	if (add_str == 1)
	{
		if (pt->collect_len > 0 && args->lengths[1] > 0 && args->args[1] != NULL)
		{
			memcpy(pt->collect+pt->collect_len, pt->sep, pt->sep_len);
			pt->collect_len+=pt->sep_len;
		}

		if (((pt->collect_len + args->lengths[1]) < 65000) && args->lengths[1] > 0 && args->args[1] != NULL)
		{
			memcpy(pt->collect+pt->collect_len, args->args[1], args->lengths[1]);
			pt->collect_len += args->lengths[1];
		}
	}

#ifdef DBG
	else
	{
		if (args->lengths[1] < 1)
		{
			fprintf(fp, "arglen 0 \n");
		}
		else
		{
			fprintf(fp, "too long totlen = %i \n",(pt->collect_len+args->lengths[1])); 
		}
	}
	fprintf(fp, "stradd_add exit , collect = %s , length = %i \n", pt->collect, pt->collect_len);
	fclose(fp);
#endif
}

char *stradd(UDF_INIT *initid, UDF_ARGS *args,char *result, unsigned long *length,char *is_null, char *error)
{
	struct stradd_struct *pt = ((struct stradd_struct *)initid->ptr);
	char *res;
#ifdef DBG
	FILE *fp;
	fp = fopen("/tmp/stradd.log","a");
	fprintf(fp, "stradd() , collect = %s len = %i \n", pt->collect,pt->collect_len);
	fclose(fp);
#endif
	*length = pt->collect_len;
	res = (char *)malloc(pt->collect_len);
	memcpy(res, pt->collect, pt->collect_len);
	return res;
}
