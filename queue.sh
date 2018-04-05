#!/bin/bash

if [ -f queue.pid ]; then
    kill -9 $(cat queue.pid)
    rm queue.pid

fi

php artisan queue:work & echo $! >> queue.pid
chmod 777 queue.pid