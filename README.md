mapa
====

Mapa připojených hráčů

Instalace
---------

1. Stáhnout a rozbalit
2. Upravit soubory v config/ složce
3. Zkopírovat example.htacces na .htaccess, nebo přidat rewrite do VirtualHostu v apachi
4. Do databáze nahrát db/structure.sql
5. Na serveru s wowkem přidat do crontabu script na vytváření seznamu připojených hráčů:

   `*/5 *   *   *   *    bash scripts/getips.sh | php scripts/xmlips.php > /var/www/ips.php`


