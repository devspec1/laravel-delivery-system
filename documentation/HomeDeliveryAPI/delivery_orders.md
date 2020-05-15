**Delivery orders**
----
  Get list of orders. This gets orders in **new** and **expired** statuses.

* **URL**

  api/delivery_orders?

* **Method:**

  `GET`
  
*  **URL Params**

   **Required:**
 
   `token=[string]`

   `distance=[string : "5" | "10" | "15"]`
   
   `latitude=[string : stringify float ]`
    
   `longitude=[string : stringify float ]`

* **Success Response:**
  
  * **Code:** 200 <br />
    **Content:** 
   ```json
   {
        "status_code": "1",
        "status_message": "Success",
        "jobs": [
            {
                "estimate_time": "1.4 Hours",
                "status": "new",
                "order_id": 17,
                "date": "06 May 2020 | 20:41",
                "pick_up": "264 Ella Rue, Haleymouth, NSW 2316",
                "drop_off": "331 Nikolaus Circle, Estellshire, NSW 2927",
                "distance": "6.29KM",
                "fee": "$99.63"
            },
            {
                "estimate_time": "1.53 Hours",
                "status": "new",
                "order_id": 16,
                "date": "06 May 2020 | 20:40",
                "pick_up": "930B Dicki Green, Port Waltonside, SA 2612",
                "drop_off": "76 \/ 27 Bayer Upper, St. Marietta, WA 2920",
                "distance": "6.34KM",
                "fee": "$99.33"
            },
            {
                "estimate_time": "3.0 Hours",
                "status": "new",
                "order_id": 18,
                "date": "06 May 2020 | 20:41",
                "pick_up": "71B Marina Parklands, Jeanneville, ACT 2606",
                "drop_off": "1 \/ 67 Jaiden Crossing, Brandyshire, ACT 2693",
                "distance": "6.43KM",
                "fee": "$77.33"
            }
        ]
    }

* **Sample Call:**

   https://dev.rideon.co/api/delivery_orders?token=token&distance=5&latitude=54.9375397&longitude=82.9292693