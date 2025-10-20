lsof | grep /var/lib/dpkg/lock && ps -e | grep -e apt -e adept | grep -v grep
