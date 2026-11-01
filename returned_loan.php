<?php 
    require_once 'includes/db.php';
    require_once 'includes/functions.php';

    $sql = "SELECT
        loans.Loan_ID,
        loans.Employee_Name,
        items.Item_Name,
        loans.Loan_Quantity,
        loans.Loan_Date,
        loans.Returned_Quantity,
        loans.Returned_Date
        FROM loans
        INNER JOIN items
        ON loans.Item_ID = items.Item_ID
        WHERE loans.Returned_Date IS NOT NULL AND (loans.Loan_Quantity = loans.Returned_Quantity)
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
            <a href="active_loan.php">Loans</a>
            <a href="returned_loan.php" class="active">Returned</a>
        </nav>
    </header>

    <main>
    <h1 class="page-title">Returned Loans</h1>

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
                    <th>Return Date</th>
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
                                <?php echo $row['Returned_Date'] ?? 'Not returned'; ?>
                            </td>
                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="8" class="text-center">
                            No Full returns Yet.
                        </td>
                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>
    </main>
</body>
</html>
