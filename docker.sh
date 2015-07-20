#192.168.59.103

docker run -P --name rprajan -i -t ubuntu /bin/bash

apt-get install apache2  
apt-get install php5 php5-mysql libapache2-mod-php5 
apt-get install mysql-server mysql-client
apt-get install vim telnet wget curl 
apt-get install pwgen 
apt-get install lynx 
apt-get install php5-curl
apt-get install git 


docker run -p 80:80 --name rprajan  -v /Users/johndondapati/Sites/:/var/www/html -i -t rprajan /bin/bash
