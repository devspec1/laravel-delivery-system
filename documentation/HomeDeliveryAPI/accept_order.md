**Accept order**
----
  Accept, cancel, pick up order, and close it.

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

   **Optional:**

   `distance=[string : "5" | "10" | "15"]`
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

  * **Code:** 200 (On ACCEPT order) <br />
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
    
  * **Code:** 200 (On PICK UP order) <br />
    **Content:**
    ```json
    {
        "status_code": "1",
        "status_message": "Order with id 16  successfully picked",
        "job_status": "picked_up"
    }
    ```
  * **Code:** 200 (On DROP OFF order) <br />
    **Content:**
    ```json
    {
        "status_code": "1",
        "status_message": "Order with id 16  successfully delivered",
        "job_status": "delivered"
    }
    ```

  * **Code:** 200 (On CANCELLING order when order in accepted status) <br />
    **Content:**
    ```json
    {
        "status_code": "1",
        "status_message": "Order with id 17  successfully cancelled",
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
                "fee": "$44.22"
            },
            {
                "estimate_time": "1.23 Hours",
                "status": "picked_up",
                "order_id": 16,
                "date": "06 May 2020 | 20:40",
                "pick_up_time": "22:39 PM",
                "pick_up": "264 Ella Rue, Haleymouth, NSW 2316",
                "drop_off": "331 Nikolaus Circle, Estellshire, NSW 2927",
                "customer_name": "Konstantinos Konst",
                "customer_phone_number": "+61923987654",
                "order_description": "Test description",
                "fee": "$99.33"
            }
        ]
    }
 
* **Error Response:**

  * **Code:** 200 <br />
    **Content:**
    ```json
    {
        "status_code":"0",
        "status_message":"Order already assigned."
    } 
    ```

  OR

  * **Code:** 200<br />
    **Content:**
    ```json
    {
        "status_code":"0",
        "status_message":"Order with id 2 successfully cancelled"
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

        assigned->new (cancellation on assigned order, if cancel=true)

        assigned->picked_up (picking up on assigned order)

        picked_up->delivered (drop off on picked_up order)

        picked_up->new (cancellation on picked_up order, if cancel=true)
