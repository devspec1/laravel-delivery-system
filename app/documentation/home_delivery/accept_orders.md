**Accept order**
----
  Acceptation of order.

* **URL**

  api/accept_order?token=token

* **Method:**
  
  `POST`
  
*  **URL Params**

   **Required:**
 
   `token=[string]`

* **Data Params**

   **Required:**
 
   `order_id=[string : strigify integer]`
  
   `latitude=[string : stringify float ]`
    
   `longitude=[string : stringify float ]`

   `distance=[string : "5" | "10" | "15"]`

   **Optional:**

   `cancel=[bool]`

  ```json
  { 
  "order_id": "2",
  "distance" : "5",
  "latitude" : "54.9375397",
  "longitude" : "82.9292693",
  "cancel" : false
  }
  ```

* **Success Response:**

  * **Code:** 200 <br />
    **Content:**
    ```json
    {
        "status_code": "1",
        "status_message": "Order with id 16  successfully assigned",
        "jobs": [
            {
                "estimate_time": "2.50 Hours",
                "status": "new",
                "order_id": 18,
                "date": "06 May 2020 | 20:41",
                "pick_up": "264 Ella Rue, Haleymouth, NSW 2316",
                    "drop_off": "331 Nikolaus Circle, Estellshire, NSW 2927",
                "distance": "6.44KM",
                "fee": "$77.33"
            }
        ]
    }
    ```
 
* **Error Response:**

  * **Code:** 200 <br />
    **Content:**
    ```json
    {
        "status_code":"0",
        "status_message":"Order already assigned."
    } 
    ```

* **Sample Call:**

  _https://dev.rideon.co/api/accept_order?token=token_

    ```json
    { 
        "order_id": "2",
        "distance" : "5",
        "latitude" : "54.9375397",
        "longitude" : "82.9292693",
        "cancel" : false
    }
    ```

* **Notes:**

    Accepting transitions:

        new->assigned (acceptation on new order)