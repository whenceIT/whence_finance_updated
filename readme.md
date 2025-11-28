## bedford

ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
FLUSH PRIVILEGES;
EXIT;


bench new-site erpnext.localhost --mariadb-root-password root

