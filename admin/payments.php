<?php include 'includes/header.php'; ?>

<h2>Payments Management</h2>

<div class="filter-controls">
  <form id="payment-filter-form" method="get">
    <div>
      <label for="status-filter">Status:</label>
      <select id="status-filter" name="status">
        <option value="">All Statuses</option>
        <option value="completed">Completed</option>
        <option value="pending">Pending</option>
        <option value="cancelled">Cancelled</option>
        <option value="refunded">Refunded</option>
      </select>
    </div>
    <div>
      <label for="date-from">Date From:</label>
      <input type="date" id="date-from" name="date_from">
    </div>
    <div>
      <label for="date-to">Date To:</label>
      <input type="date" id="date-to" name="date_to">
    </div>
    <div>
      <button type="submit">Filter</button>
      <button type="button" id="reset-filter">Reset</button>
    </div>
  </form>
</div>

<table id="payments-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Booking ID</th>
      <th>Customer</th>
      <th>Amount</th>
      <th>Method</th>
      <th>Status</th>
      <th>Transaction ID</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Initialize the WHERE clause
    $whereClause = "1=1"; // This will always evaluate to true, allowing us to append conditions with AND

    // Apply status filter if provided
    if (isset($_GET['status']) && !empty($_GET['status'])) {
      $status = $conn->real_escape_string($_GET['status']);
      $whereClause .= " AND p.status = '$status'";
    }

    // Apply date filters if provided
    if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
      $dateFrom = $conn->real_escape_string($_GET['date_from']);
      $whereClause .= " AND p.payment_date >= '$dateFrom'";
    }

    if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
      $dateTo = $conn->real_escape_string($_GET['date_to']);
      $whereClause .= " AND p.payment_date <= '$dateTo'";
    }

    $sql = "SELECT p.*, b.id as booking_id, u.full_name as customer_name 
                FROM payments p 
                LEFT JOIN bookings b ON p.booking_id = b.id 
                LEFT JOIN users u ON b.user_id = u.id 
                WHERE $whereClause
                ORDER BY p.payment_date DESC";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><a href='booking_details.php?id=" . $row['booking_id'] . "'>" . $row['booking_id'] . "</a></td>";
        echo "<td>" . $row['customer_name'] . "</td>";
        echo "<td>$" . number_format($row['amount'], 2) . "</td>";
        echo "<td>" . ucfirst($row['payment_method']) . "</td>";

        // Status with appropriate class for styling
        $statusClass = '';
        switch ($row['status']) {
          case 'completed':
            $statusClass = 'status-completed';
            break;
          case 'pending':
            $statusClass = 'status-pending';
            break;
          case 'cancelled':
            $statusClass = 'status-cancelled';
            break;
          case 'refunded':
            $statusClass = 'status-refunded';
            break;
        }

        echo "<td class='" . $statusClass . "'>" . ucfirst($row['status']) . "</td>";
        echo "<td>" . $row['transaction_id'] . "</td>";
        echo "<td>" . date('M d, Y', strtotime($row['payment_date'])) . "</td>";
        echo "<td>
                        <button class='view-btn' data-id='" . $row['id'] . "'>View</button>
                        <button class='update-status-btn' data-id='" . $row['id'] . "'>Update Status</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='9'>No payments found</td></tr>";
    }
    ?>
  </tbody>
</table>

<!-- View Payment Modal -->
<div id="view-payment-modal" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Payment Details</h3>
    <div id="payment-details">
      <p><strong>Payment ID:</strong> <span id="modal-payment-id"></span></p>
      <p><strong>Booking ID:</strong> <span id="modal-booking-id"></span></p>
      <p><strong>Customer:</strong> <span id="modal-customer"></span></p>
      <p><strong>Amount:</strong> <span id="modal-amount"></span></p>
      <p><strong>Method:</strong> <span id="modal-method"></span></p>
      <p><strong>Status:</strong> <span id="modal-status"></span></p>
      <p><strong>Transaction ID:</strong> <span id="modal-transaction-id"></span></p>
      <p><strong>Payment Date:</strong> <span id="modal-date"></span></p>
      <p><strong>Notes:</strong> <span id="modal-notes"></span></p>
    </div>
  </div>
</div>

<!-- Update Status Modal -->
<div id="update-status-modal" class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Update Payment Status</h3>
    <form id="update-status-form">
      <input type="hidden" id="status-payment-id" name="payment_id">
      <div>
        <label for="payment-status">Status:</label>
        <select id="payment-status" name="status" required>
          <option value="completed">Completed</option>
          <option value="pending">Pending</option>
          <option value="cancelled">Cancelled</option>
          <option value="refunded">Refunded</option>
        </select>
      </div>
      <div>
        <label for="status-notes">Notes:</label>
        <textarea id="status-notes" name="notes" rows="3"></textarea>
      </div>
      <div>
        <button type="submit">Update Status</button>
        <button type="button" class="cancel-btn">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Reset filter
    document.getElementById('reset-filter').addEventListener('click', function() {
      document.getElementById('status-filter').value = '';
      document.getElementById('date-from').value = '';
      document.getElementById('date-to').value = '';
      document.getElementById('payment-filter-form').submit();
    });

    // Set filter values from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status')) {
      document.getElementById('status-filter').value = urlParams.get('status');
    }
    if (urlParams.has('date_from')) {
      document.getElementById('date-from').value = urlParams.get('date_from');
    }
    if (urlParams.has('date_to')) {
      document.getElementById('date-to').value = urlParams.get('date_to');
    }

    // View payment details
    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach(button => {
      button.addEventListener('click', function() {
        const paymentId = this.getAttribute('data-id');
        fetchPaymentDetails(paymentId);
      });
    });

    // Update payment status
    const updateButtons = document.querySelectorAll('.update-status-btn');
    updateButtons.forEach(button => {
      button.addEventListener('click', function() {
        const paymentId = this.getAttribute('data-id');
        document.getElementById('status-payment-id').value = paymentId;
        document.getElementById('update-status-modal').style.display = 'block';
      });
    });

    // Close modals
    const closeButtons = document.querySelectorAll('.close, .cancel-btn');
    closeButtons.forEach(button => {
      button.addEventListener('click', function() {
        document.getElementById('view-payment-modal').style.display = 'none';
        document.getElementById('update-status-modal').style.display = 'none';
      });
    });

    // Submit status update form
    document.getElementById('update-status-form').addEventListener('submit', function(e) {
      e.preventDefault();

      const paymentId = document.getElementById('status-payment-id').value;
      const status = document.getElementById('payment-status').value;
      const notes = document.getElementById('status-notes').value;

      updatePaymentStatus(paymentId, status, notes);
    });
  });

  function fetchPaymentDetails(id) {
    fetch('actions/get_payment.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('modal-payment-id').textContent = data.payment.id;
          document.getElementById('modal-booking-id').textContent = data.payment.booking_id;
          document.getElementById('modal-customer').textContent = data.payment.customer_name;
          document.getElementById('modal-amount').textContent = '$' + parseFloat(data.payment.amount).toFixed(2);
          document.getElementById('modal-method').textContent = data.payment.payment_method;
          document.getElementById('modal-status').textContent = data.payment.status;
          document.getElementById('modal-transaction-id').textContent = data.payment.transaction_id;
          document.getElementById('modal-date').textContent = data.payment.payment_date;
          document.getElementById('modal-notes').textContent = data.payment.notes || 'No notes';

          document.getElementById('view-payment-modal').style.display = 'block';
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while fetching payment details.');
      });
  }

  function updatePaymentStatus(id, status, notes) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', status);
    formData.append('notes', notes);

    fetch('actions/update_payment_status.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Payment status updated successfully!');
          window.location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating payment status.');
      });
  }
</script>

<?php include 'includes/footer.php'; ?>