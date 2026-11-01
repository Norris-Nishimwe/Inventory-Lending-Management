<?php
    //include 'includes/db.php';
   // require 'hit.php';
    function add_item($conn, $name, $quantity){

        if(empty($name)){
            return "Item name cannot be empty.";
        }
        if($quantity < 0){
            return "Quantity cannot be negative.";
        }

        $sql = "INSERT INTO items (Item_Name, Quantity, Total_Quantity) VALUES (?, ?, ?)";
        //$params = array($name, $quantity);

        $stmt = mysqli_prepare($conn, $sql);

        if(!$stmt) {
            return "Error preparing statement: " . mysqli_connect_error();
        }
        mysqli_stmt_bind_param($stmt, "sii", $name, $quantity, $quantity);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return "Item added successfully.";
        }
    }

    function delete_item($conn, $Item_ID) {
        $sql = "UPDATE items
            SET Is_Deleted = TRUE
            WHERE Item_ID = ?
            AND Is_Deleted = FALSE
            AND NOT EXISTS (
                SELECT 1
                FROM loans
                WHERE loans.Item_ID = items.Item_ID
                AND loans.Returned_Quantity < loans.Loan_Quantity
            )";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            return "Error preparing query: " . mysqli_error($conn);
        }

        mysqli_stmt_bind_param($stmt, "i", $Item_ID);

        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return "Error: " . $error;
        }

        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($affected > 0) {
            return "Item successfully removed from active inventory.";
        }

        return "Item not found, already deleted, or still has outstanding loans.";
    }


    function update_item($conn, $name, $quantity, $ID){
        if(empty($name) || ($quantity < 0)){
            return "Item name and quantity cannot be empty.";
        }
        $sql = "UPDATE items SET Item_Name = ?, QUANTITY = ?, Total_Quantity = ? WHERE Item_ID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if(!$stmt) {
            return "Error preparing statement: " . mysqli_connect_error();
        }
        mysqli_stmt_bind_param($stmt, "siii", $name, $quantity,  $quantity, $ID);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return "Item updated successfully.";
        }
    }

    function edit_item($conn, $ID){
        $sql = "SELECT * FROM items WHERE Item_ID = $ID AND Is_Deleted = FALSE LIMIT 1";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

    function create_loan($conn, $Item_ID, $Borrower_Name, $Quantity){

        $Borrower_Name = trim($Borrower_Name);
        if ($Item_ID <= 0) {
            return "Please select a valid item.";
        }

        if ($Borrower_Name === "") {
            return "Borrower's name cannot be empty.";
        }

        if ($Quantity <= 0) {
            return "Quantity must be at least 1.";
        }
        

        if (!mysqli_begin_transaction($conn)) {
            return "Error starting transaction: " . mysqli_error($conn);
        }

        $sql = "UPDATE items
            SET Quantity = Quantity - ?
            WHERE Item_ID = ? AND Quantity >= ?";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            mysqli_rollback($conn);
            return "Error preparing stock update: " . mysqli_error($conn);
        }

        mysqli_stmt_bind_param($stmt, "iii", $Quantity, $Item_ID, $Quantity);

        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            mysqli_rollback($conn);
            return "Error updating stock: " . $error;
        }

        $updated = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($updated !== 1) {
            mysqli_rollback($conn);
            return "The item does not exist or there is not enough stock available.";
        
        }

        if (!mysqli_begin_transaction($conn)) {
            return "Error starting transaction: " . mysqli_error($conn);
        }
        $sql = "INSERT INTO loans (Item_ID, Employee_Name, Loan_Quantity, Loan_Date) VALUES (?, ?, ?, NOW())";
        
        $stmt = mysqli_prepare($conn, $sql);

         if (!$stmt) {
            $error = mysqli_error($conn);
            mysqli_rollback($conn);

            return "Error preparing loan insertion: " . $error;
        }

        mysqli_stmt_bind_param($stmt, "isi",
            $Item_ID,
            $Borrower_Name,
            $Quantity
        );

        if (!mysqli_stmt_execute($stmt)) {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            mysqli_rollback($conn);

            return "Error inserting loan: " . $error;
        }

        mysqli_stmt_close($stmt);

        // STEP 4: Commit transaction
        if (!mysqli_commit($conn)) {
            $error = mysqli_error($conn);
            mysqli_rollback($conn);

            return "Error committing loan: " . $error;
        }

        return "Loan created successfully.";
    }

    
    
    function return_loan($conn, $Loan_ID, $Return_Quantity) {

        // 1. Validate the input
        $Loan_ID = (int) $Loan_ID;
        $Return_Quantity = (int) $Return_Quantity;

        if ($Loan_ID <= 0 || $Return_Quantity <= 0) {
            return "Error: Invalid loan ID or return quantity.";
        }

        $transactionStarted = false;

        try {

            // 2. Start the transaction
            mysqli_begin_transaction($conn);
            $transactionStarted = true;

            // 3. Get the loan details and lock the row
            $sql = "SELECT Item_ID, Loan_Quantity, Returned_Quantity
                    FROM loans
                    WHERE Loan_ID = ?
                    FOR UPDATE";

            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Error preparing loan query: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "i", $Loan_ID);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $loan = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            // 4. Check whether the loan exists
            if (!$loan) {
                throw new Exception("Error: Loan record not found.");
            }

            $Item_ID = (int) $loan['Item_ID'];
            $Loan_Quantity = (int) $loan['Loan_Quantity'];
            $Returned_Quantity = (int) $loan['Returned_Quantity'];

            // 5. Calculate the remaining quantity
            $Remaining_Quantity = $Loan_Quantity - $Returned_Quantity;

            if ($Remaining_Quantity === 0) {
                throw new Exception("This loan has already been fully returned.");
            }

            // 6. Prevent returning more than the outstanding quantity
            if ($Return_Quantity > $Remaining_Quantity) {
                throw new Exception(
                    "Error: You can only return " . $Remaining_Quantity . " more item(s)."
                );
            }

            // 7. Calculate the new returned quantity
            $New_Returned_Quantity = $Returned_Quantity + $Return_Quantity;

            // 8. Update the loan record
            // Set Return_DateTime only when the entire loan is returned
            if ($New_Returned_Quantity == $Loan_Quantity) {

                $sql = "UPDATE loans
                        SET Returned_Quantity = ?,
                            Returned_Date = NOW()
                        WHERE Loan_ID = ?";

            } else {

                $sql = "UPDATE loans
                        SET Returned_Quantity = ?
                        WHERE Loan_ID = ?";
            }

            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Error preparing loan update: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $New_Returned_Quantity,
                $Loan_ID
            );

            if (!mysqli_stmt_execute($stmt)) {
                $error = mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
                throw new Exception("Error updating loan: " . $error);
            }

            mysqli_stmt_close($stmt);

            // 9. Increase the available quantity in the items table
            // Do not allow available quantity to exceed total quantity
            $sql = "UPDATE items
                    SET Quantity = Quantity + ?
                    WHERE Item_ID = ?
                    AND Quantity + ? <= Total_Quantity";

            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception("Error preparing stock update: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "iii",
                $Return_Quantity,
                $Item_ID,
                $Return_Quantity
            );

            if (!mysqli_stmt_execute($stmt)) {
                $error = mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
                throw new Exception("Error updating available quantity: " . $error);
            }

            // Check whether the item stock was actually updated
            if (mysqli_stmt_affected_rows($stmt) !== 1) {
                mysqli_stmt_close($stmt);
                throw new Exception(
                    "Error: Item not found or returned quantity would exceed total stock."
                );
            }

            mysqli_stmt_close($stmt);

            // 10. Commit the transaction
            mysqli_commit($conn);
            $transactionStarted = false;

            if ($New_Returned_Quantity == $Loan_Quantity) {
                return "Success: All items have been returned successfully.";
            }

            return "Success: " . $Return_Quantity .
                " item(s) returned. " .
                ($Loan_Quantity - $New_Returned_Quantity) .
                " item(s) are still outstanding.";

        } catch (Throwable $e) {

            // Roll back all changes if something goes wrong
            if ($transactionStarted) {
                mysqli_rollback($conn);
            }

            return $e->getMessage();
        }
    }

?>