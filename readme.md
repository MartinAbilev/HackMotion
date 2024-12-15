# Setup docker image
``` bash
    sh run sh
```

# Set custom theme in WordPress
    - In APEERANCE select from templates "twentytwentyfive/math"

# Create DB table
    - in functions.php uncomment line 216 	// create_video_timestamps_table();
    - open page localhost:8000/?0 it will create table and insert first event
    - in case everything works open http://localhost:8000/wp-content/themes/math/templates/stats.html
      - data exsample
       ```json
        {
        "message": "Records retrieved successfully",
        "data": [
            {
            "id": "363",
            "userid": "07cbe711-0d28-4b56-9fda-503132eb78b2",
            "event": "video_ended",
            "urlref": "http://localhost:8000/?0",
            "browser": "Chrome",
            "device": "Desktop",
            "ip_address": "192.168.65.1",
            "timestamp": "2024-12-15 10:49:49"
            },
        ]
        }
        ```
    - coment agen line 216 and refresh localhost:8000/?0

# Params  is read  0..3  to dynamic chage for break`s
    - localhost:8000/?0 = break Par
    - localhost:8000/?1 = break 80
    - localhost:8000/?2 = break 90
    - localhost:8000/?3 = break 100
