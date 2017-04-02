#!/bin/bash
#Script bas adds new VHost to local server apache2. Ubuntu 16.04
echo "Adding new vhost";
echo "Write name site:"
read namePage;
echo "DocumentRoot [default '/']:"
read DocumentRoot
cd /etc/apache2/sites-available/
touch $namePage.conf

echo "<VirtualHost *:80>
	ServerAdmin micmat@localhost
	DocumentRoot /var/www/html/$namePage$DocumentRoot
	ServerName www.$namePage.loc
	ServerAlias $namePage.loc

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined


	</VirtualHost>" > $namePage.conf

ln -s -r $namePage.conf ../sites-enabled/$namePage.conf
cd /etc/
newHost="\\
127.0.0.1 www.$namePage.loc\\
127.0.0.1 $namePage.loc"
sed "5s/$/$newHost/g" hosts 1> hosts2
rm hosts
mv hosts2 hosts
service apache2 restart
exit
