# Inventory-Lending-Management
1. Setup and execution
   - Visual Studio Code ( get HTML, CSS, JAVASCRIP, PHP extensions)
   - MySQL workbench(https://www.mysql.com/downloads/)
   - XAMPP (version 3.3.0 or later)
2. How To run this project
   -clone this repository in your web server's root ( XAMPP: htdocs/...)
   - Open MySQL Workbench, Crete a database called 'inventory_db' and use the schema.sql file.
   - open the project in visual Studio Code and change the 'conn_example.php' file's name to 'db.php'.       Change the password to the your MySQL workbench password.
   - Open XAMPP and start the Apache and MySQL modules and open (http://localhost/inventory&lendingmanagementsystem/main.html) in your browser.
     
3. Implementation Scope (required)
   - Add, edit,delete and view items.
   - Create, return, and view active and returned loans in a list with transaction date and time.
   - store transaction in data in a Database.
   -                Optional
   - partial returns
   - No edit or delete an item with an active loan.
4. Structure and Data

   - Data is stored in a database
   - Before a loan is created, the quantity is checked against the stored quantity in the  database.
   - the Available quantity is updated to the new quantity after a successfull laon.
5. Design decisions
   - I chose to allow partial returns because waiting for the whole return might slow or stop other workers from getting items.
6. problem solving
   - The most difficult problem was how to process partial returns and update the database everytime it occurs.
   - handling errors.
7. Use of Tools
   - I used AI mostly for error handling and syntax verification.
   - I used open source tools as well like W3schools.com.
8. Limitations & improvement
   - I had no limitations other than time.
   - I would add more error handling, add an ID for each item to know which item was borrowed by which person at a certain time.
   - add a login system.  
10. 
