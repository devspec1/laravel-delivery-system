**Accept order**
----
  Pick up order, cancel and and close it.

* **URL**

  api/proceed_order?token=token

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

   `cancel=[bool]`

  ```json
  { 
    "order_id": "2",
    "latitude" : "54.9375397",
    "longitude" : "82.9292693",
    "cancel" : false
  }
  ```

* **Success Response:**
    
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
        "job_status": "new"
    }
 
* **Error Response:**

  * **Code:** 200 <br />
    **Content:**
    ```json
    {
        "status_code":"0",
        "status_message":"Order already delivered."
    } 
    ```

  * **Code:** 200 <br />
    **Content:**
    ```json
    {
        "status_code":"0",
        "status_message":"Invalid credentials"
    } 
    ```

* **Sample Call:**

  _https://dev.rideon.co/api/proceed_order?token=token_

    ```json
    { 
        "order_id": "2",
        "latitude" : "54.9375397",
        "longitude" : "82.9292693",
        "cancel" : false
    }
    ```

* **Notes:**

    Accepting transitions:

        assigned->new (cancellation on assigned order, if cancel=true)

        assigned->picked_up (picking up on assigned order)

        picked_up->delivered (drop off on picked_up order)

        picked_up->new (cancellation on picked_up order, if cancel=true)