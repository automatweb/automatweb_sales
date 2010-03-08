#include <stdio.h>
#include <signal.h>
#include <sys/types.h>
#include <unistd.h>

int main(int argc, char **argv)
{
	pid_t fres;
	FILE *fp;

	printf("execing %s \n", argv[1]);
	// go to daemon mode
	fres = fork();
	if (fres == -1)
	{
		perror("fork");
	}
	else
	if (fres != 0)
	{
		return 0;
	}

	setuid(geteuid());
	// this should detach us from the parent process
	setsid();
	// this should avoid the child process from getting killed when the parent is killed
	signal(SIGHUP, SIG_IGN);

	daemon(0,0);

	// now, every 30 seconds, check for the file  /tmp/ap_reboot
	// if it exists, then exec the command given as an argument
	while(1)
	{
		fp = fopen("/tmp/ap_reboot", "r");
		if (fp)
		{
			system(argv[1]);
			fclose(fp);
			unlink("/tmp/ap_reboot");
			sleep(60);
		}
		else
		{
			sleep(30);
		}
	}
}
