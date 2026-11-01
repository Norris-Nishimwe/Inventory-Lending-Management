<?php 
    require_once 'includes/db.php';
    require_once 'includes/functions.php';

    if (isset($_POST['returnLoan'])) {
        $Loan_ID = (int) $_POST['loanID'];
        $Return_Quantity = (int) $_POST['Return_Quantity'];

        if ($Return_Quantity > 0) {

            $message = return_loan(
                $conn,
                $Loan_ID,
                $Return_Quantity
            );

        } else {
            $message = "Please enter a valid return quantity.";
        }
    }
    $sql = "SELECT
        loans.Loan_ID,
        loans.Employee_Name,
        items.Item_Name,
        loans.Loan_Quantity,
        loans.Loan_Date,
        loans.Returned_Quantity,
        (loans.Loan_Quantity - loans.Returned_Quantity) AS Remaining_Quantity,
        loans.Returned_Date
        FROM loans
        INNER JOIN items
        ON loans.Item_ID = items.Item_ID
        WHERE loans.Returned_Quantity < loans.Loan_Quantity
        ORDER BY loans.Loan_Date DESC";

    $result = mysqli_query($conn, $sql);
    //$row = mysqli_fetch_assoc($result);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>return</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="topbar">
        <a href="main.html" class="brand">
            <span class="brand-name">Equipement Lending</span>
        </a>
        <nav class="nav">
            <a href="main.html">Home</a>
            <a href="items.php">Items</a>
            <a href="add_item.php">Add Item</a>
            <a href="loan.php">New Loan</a>
            <a href="active_loan.php" class="active">Loans</a>
            <a href="returned_loan.php">Returned</a>
        </nav>
    </header>

    <main>
    <h1 class="page-title">Active Loans</h1>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info" style="animation: disappear 0.5s ease 5s forwards"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <p class="text-danger mt-3 mb-0">
            No items are currently available for borrowing.
        </p>
    <?php endif; ?>

    <div>

        <table class="table table-hover table-striped align-middle">

            <thead class="table-dark">
                <tr>
                    <th>Name & Surname</th>
                    <th>Item</th>
                    <th>Loan Quantity</th>
                    <th>Loan Date</th>
                    <th>Returned Quantity</th>
                    <th>Remaining</th>
                    <th>Return Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if (mysqli_num_rows($result) > 0) { ?>

                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                        <tr class="loan-row">
                            <td><?php echo htmlspecialchars($row['Employee_Name']); ?></td>
                            <td><?php echo htmlspecialchars($row['Item_Name']); ?></td>
                            <td><?php echo $row['Loan_Quantity']; ?></td>
                            <td><?php echo $row['Loan_Date']; ?></td>
                            <td><?php echo $row['Returned_Quantity']; ?></td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <?php echo $row['Remaining_Quantity']; ?>
                                </span>
                            </td>
                            <td>
                                <?php echo $row['Returned_Date'] ?? 'Not returned'; ?>
                            </td>
                            <td>
                                <button type="button"
                                        style="cursor:pointer"
                                        class="btn btn-primary btn-sm return-btn"
                                        data-id="<?php echo $row['Loan_ID']; ?>"
                                        data-borrower="<?php echo htmlspecialchars($row['Employee_Name'], ENT_QUOTES); ?>"
                                        data-item="<?php echo htmlspecialchars($row['Item_Name'], ENT_QUOTES); ?>"
                                        data-loan="<?php echo $row['Loan_Quantity']; ?>"
                                        data-returned="<?php echo $row['Returned_Quantity']; ?>"
                                        data-remaining="<?php echo $row['Remaining_Quantity']; ?>">

                                    <i class="bi bi-pencil-square"></i> Return Item

                                </button>
                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="8" class="text-center">
                            No pending returns found.
                        </td>
                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>
    </main>

    <div class="modal fade" id="returnModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Return Borrowed Item</h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="active_loan.php">

                    <div class="modal-body">

                        <input type="hidden"
                            name="loanID"
                            id="loanId">

                        <div class="mb-3">
                            <label class="form-label">Borrower</label>
                            <input type="text"
                                id="employename"
                                class="form-control"
                                readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Item</label>
                            <input type="text"
                                id="itemname"
                                class="form-control"
                                readonly>
                        </div>

                        <div class="row">

                            <div class="col-4">
                                <label class="form-label">Borrowed</label>
                                <input type="text"
                                    id="loanQuantity"
                                    class="form-control"
                                    readonly>
                            </div>

                            <div class="col-4">
                                <label class="form-label">Returned</label>
                                <input type="text"
                                    id="returnedQuantity"
                                    class="form-control"
                                    readonly>
                            </div>

                            <div class="col-4">
                                <label class="form-label">Remaining</label>
                                <input type="text"
                                    id="remainingQuantity"
                                    class="form-control"
                                    readonly>
                            </div>

                        </div>

                        <div class="mt-3">
                            <label class="form-label">
                                Quantity Being Returned
                            </label>

                            <input type="number"
                                name="Return_Quantity"
                                id="returnQuantity"
                                class="form-control"
                                min="1"
                                required>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Return Date & Time</label>

                            <input type="text"
                                id="returnDate"
                                class="form-control"
                                readonly>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                                name="returnLoan"
                                class="btn btn-success">
                            Confirm Return
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>

        
        const returnModal = new bootstrap.Modal(
            document.getElementById('returnModal')
        );

        document.querySelectorAll('.return-btn').forEach(button => {

            button.addEventListener('click', function() {

                // Populate borrower and item information
                document.getElementById('loanId').value = this.dataset.id;

                document.getElementById('employename').value =
                    this.dataset.borrower;

                document.getElementById('itemname').value =
                    this.dataset.item;

                // Populate loan quantities
                document.getElementById('loanQuantity').value =
                    this.dataset.loan;

                document.getElementById('returnedQuantity').value =
                    this.dataset.returned;

                document.getElementById('remainingQuantity').value =
                    this.dataset.remaining;

                // Configure return quantity input
                const returnInput = document.getElementById('returnQuantity');

                returnInput.value = '';
                returnInput.max = this.dataset.remaining;

                // Display current date and time
                document.getElementById('returnDate').value =
                    new Date().toLocaleString();

                // Open modal
                returnModal.show();

            });

        });


    </script>
</body>
</html>
