echo 'SHELL=/bin/sh
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin

0 0 * * * root (\
    find /root/.fuse -name "task-*" -type f -mtime +7 -exec rm {} \; \
    find /home/fuse/.fuse -name "task-*" -type f -mtime +7 -exec rm {} \;\
) 2>&1' > /etc/cron.d/fuse-task-cleanup
chmod 644 /etc/cron.d/fuse-task-cleanup
