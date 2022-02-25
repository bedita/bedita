#!/usr/bin/env sh

################################################################################
# Shell script to update permissions on BEdita4
# Shell user and web server have rwx permissions on tmp/ and logs/
################################################################################

HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`

if [ -z "$HTTPDUSER" ]; then
    echo "Web server user not found, verify that a webserver service (like Apache2) is up & running"
    exit 1;
fi

echo "Web server user is: $HTTPDUSER"

echo "setfacl -R -m u:${HTTPDUSER}:rwx tmp"
setfacl -R -m u:${HTTPDUSER}:rwx tmp

echo "setfacl -R -d -m u:${HTTPDUSER}:rwx tmp"
setfacl -R -d -m u:${HTTPDUSER}:rwx tmp

echo "setfacl -R -m u:${HTTPDUSER}:rwx logs"
setfacl -R -d -m u:${HTTPDUSER}:rwx logs
