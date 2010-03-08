#!/bin/bash
./configure  --with-apxs=/usr/local/apache/bin/apxs \
	     --enable-sockets \
	     --with-ftp \
	     --with-gd=/usr \
	     --with-imap=/usr \
             --enable-ftp \
             --with-config-file-path=/etc/apache \
             --enable-trans-sid \
             --enable-sysvshm \
             --enable-sysvsem \
	     --enable-sigchild \
             --enable-calendar \
             --enable-bcmath \
             --with-mysql=/usr/local
             
             
