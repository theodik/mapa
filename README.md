mapa
====

Mapa připojených hráčů


Crontab

*/5 *   *   *   *    bash scripts/getips.sh | php scripts/xmlips.php > /var/www/ips.php
