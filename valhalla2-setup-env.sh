#!/usr/bin/env bash
SOURCE=${BASH_SOURCE[0]}
while [ -L "$SOURCE" ]; do # resolve $SOURCE until the file is no longer a symlink
	DIR=$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )
	SOURCE=$(readlink "$SOURCE")
	[[ $SOURCE != /* ]] && SOURCE=$DIR/$SOURCE # if $SOURCE was a relative symlink, we need to resolve it relative to the path where the symlink file was located
done
DIR=$( cd -P "$( dirname "$SOURCE" )" >/dev/null 2>&1 && pwd )


# File permissions
chmod 555 $DIR
chown www-data:www-data -R $DIR

echo "-->Changed file permissions for Valhalla to -r--r--r-- www-data:www-data"
# echo "===================================================================="


# Image upload directory permissions
IMAGEDIR=${DIR}"./assets/images/profile_pics/upload/"
# echo $IMAGEDIR
mkdir $IMAGEDIR
chmod 775 $IMAGEDIR
chown www-data:www-data -R $IMAGEDIR

echo "-->Created: " $IMAGEDIR 
echo "-->Changed file permissions to rwxrwxr-x www-data:www-data"
# echo "===================================================================="

POSTIMAGEDIR=${DIR}"./assets/images/posts"
mkdir $POSTIMAGEDIR
chmod 775 $POSTIMAGEDIR
chown www-data:www-data -R $POSTIMAGEDIR

# Create .htaccess
cd $DIR
HTACCESS=".htaccess"
touch $HTACCESS
echo 'RewriteEngine On
RewriteRule ^([a-zA-Z0-9_-]+)$ profile.php?profile_username=$1
RewriteRule ^([a-zA-Z0-9_-]+)/$ profile.php?profile_username=$1

RewriteEngine On
Options +FollowSymLinks
RewriteCond %{THE_REQUEST} ^.*/index.php
RewriteRule ^(.*)index.php$ /$1 [R=301,L]' >> $HTACCESS
chmod 0644 $HTACCESS
chown root:root -R $HTACCESS
echo "-->Created: " $HTACCESS 
echo "-->Changed file permissions to -rw-r--r-- root root"
# Ideally this should be rewritten as a rule in a future update!

# Install PHP modules
if [ "$EUID" -ne 0 ]
	then echo "Please run as root to
	* install PHP modules"
	exit
fi

# PHP 8.1 is not loading posts currently
sudo apt install php7.4 php7.4-mysql php7.4-gd

sudo a2enmod rewrite
sudo a2enmod ssl

# Finished Message
echo "===================================================================="
echo "Finished environment setup!"
echo "Move `Valhalla2` folder into the root of your web directory!"

sleep 1
echo "!!!!!!!!!!!!!!!!! WARNING !!!!!!!!!!!!!!!!!"
echo "Delete setup-env.sh for security reasons"
chmod 444 setup-env.sh
chown root:root setup-env.sh
