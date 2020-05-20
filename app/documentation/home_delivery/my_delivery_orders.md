**Delivery orders**
----
  Get list of orders. This gets orders in **assigned** and **picked_up** statuses of driver.

* **URL**

  api/my_delivery_orders?token=token

* **Method:**

  `GET`
  
*  **URL Params**

   **Required:**
 
   `token=[string]`


* **Success Response:**
  
  * **Code:** 200 <br />
    **Content:** 
   ```json
   {
        "status_code": "1",
        "status_message": "Success",
        "jobs": [
            {
                "estimate_time": "Expired",
                "status": "assigned",
                "order_id": 14,
                "date": "30 Apr 2020 | 04:31",
                "pick_up_time": "06:31 AM",
                "pick_up": "Ulitsa Petukhova, 138, Novosibirsk, Novosibirskaya oblast', Russia, 630119",
                "drop_off": "Ulitsa Kotovskogo, 40\/1, Novosibirsk, Novosibirskaya oblast', Russia, 630108",
                "customer_name": "Konstantin LastOne",
                "customer_phone_number": "+610465498778",
                "order_description": "Test description",
                "distance": "0KM",
                "fee": "$44.22"
            },
            {
                "estimate_time": "0.47 Hours",
                "status": "assigned",
                "order_id": 17,
                "date": "06 May 2020 | 20:41",
                "pick_up_time": "21:50 PM",
                "pick_up": "264 Ella Rue, Haleymouth, NSW 2316",
                "drop_off": "331 Nikolaus Circle, Estellshire, NSW 2927",
                "customer_name": "Konstantinos Konst",
                "customer_phone_number": "+61923987654",
                "order_description": "Test description",
                "distance": "0KM",
                "fee": "$99.63"
            },
            {
                "estimate_time": "1.36 Hours",
                "status": "picked_up",
                "order_id": 16,
                "date": "06 May 2020 | 20:40",
                "pick_up_time": "22:39 PM",
                "pick_up": "930B Dicki Green, Port Waltonside, SA 2612",
                "drop_off": "76 \/ 27 Bayer Upper, St. Marietta, WA 2920",
                "customer_name": "Konstantinos Konst",
                "customer_phone_number": "+61923987654",
                "order_description": "Test description",
                "distance": "0KM",
                "fee": "$99.33"
            }
        ]
    }

* **Sample Call:**

   https://dev.rideon.co/api/my_delivery_orders?token=token