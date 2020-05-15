**Upgrade subscription**
----
  Upgrade subscription to **Member driver** subscripton type.

* **URL**

  api/upgrade_subscription?

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
        "status_message": "Subscribed Successfully",
        "subscription": "Member driver",
    }

* **Sample Call:**

   https://dev.rideon.co/api/upgrade_subscription?token=token