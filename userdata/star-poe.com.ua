--- 
customlog: 
  - 
    format: combined
    target: /etc/apache2/logs/domlogs/star-poe.com.ua
  - 
    format: "\"%{%s}t %I .\\n%{%s}t %O .\""
    target: /etc/apache2/logs/domlogs/star-poe.com.ua-bytes_log
documentroot: /home/starp56/public_html
group: starp56
hascgi: 1
homedir: /home/starp56
ip: 51.77.52.225
owner: root
phpopenbasedirprotect: 1
phpversion: ea-php80
port: 80
scriptalias: 
  - 
    path: /home/starp56/public_html/cgi-bin
    url: /cgi-bin/
serveradmin: webmaster@star-poe.com.ua
serveralias: mail.star-poe.com.ua www.star-poe.com.ua
servername: star-poe.com.ua
usecanonicalname: 'Off'
user: starp56
