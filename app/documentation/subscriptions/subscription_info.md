**Subscription Info**
----
  Get current subscription. For driver it should return **Driver only** or **Member driver** subscripton type.

* **URL**

  api/subscription_info?

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
        "status_message": "Listed Successfully",
        "subscription": "Driver only",
    }

* **Sample Call:**

   https://dev.rideon.co/api/subscription_info?token=token