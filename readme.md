# Dependencies

- php
- nginx

# live feed install

```
git clone https://github.com/jacksonliam/mjpg-streamer.git
cd mjpg-streamer/mjpg-streamer-experimental/
sudo apt-get install cmake python-imaging libjpeg-dev
make CMAKE_BUILD_TYPE=Debug
sudo make install
```

then run

```
screen
/var/lib/mjpg-streamer/mjpg_streamer -o "output_http.so -w ./www" -i "input_raspicam.so"
```

add proxypass to nginx config

```
location /webcam {
    proxy_pass http://localhost:8080/?action=stream;
}
```

# webcam home page

## setup

```
ln camera /var/www/camera -s
```

## cron setup 

```
ln camera webcam.cron /etc/cron.d/webcam
```
