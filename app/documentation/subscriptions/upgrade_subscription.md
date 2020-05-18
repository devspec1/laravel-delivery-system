**Upgrade subscription**
----
  Upgrade subscription to **Member driver** subscripton type.

* **URL**

  api/upgrade_subscription?

* **Method:**

  `POST`
  
*  **URL Params**

   **Required:**
 

   `token=[string]`

* **Data Params**

   **Required:**
 
   `country=[string]` 
   
   `card_name=[string]` 
    
   `payment_method=[string]`

   `email=[string]`

  ```json
  { 
    "country": "Australia",
    "card_name" : "Konstantin N",
    "payment_method" : "card_19yUNL2eZvKYlo2CNGsN6EWH",
    "email" : "email@mail.com"
  }
  ```

* **Success Response:**
  
  * **Code:** 200 <br />
    **Content:** 
   ```json
   {
        "status_code": "1",
        "status_message": "Subscribed Successfully",
        "subscription": "Member driver",
    }
    ```

  * **Code:** 200 <br />
    **Content:** 
   ```json
   {
        "status_code": "1",
        "status_message": "Upgraded Successfully",
        "subscription": "Member driver",
    }
    ```

* **Sample Call:**

   https://dev.rideon.co/api/upgrade_subscription?token=token

  ```json
  { 
    "country": "Australia",
    "card_name" : "Konstantin N",
    "payment_method" : "card_19yUNL2eZvKYlo2CNGsN6EWH",
    "email" : "email@mail.com"
  }
  ```