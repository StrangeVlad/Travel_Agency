<?php include 'includes/header.php'; ?>

<head>
  <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<h2>Coupons Management</h2>

<div class="action-buttons">
  <button id="add-coupon-btn">Add New Coupon</button>
</div>

<div id="add-coupon-form" style="display: none;">
  <h3>Add New Coupon</h3>
  <form id="couponForm" method="post" action="actions/add_coupon.php">
    <div>
      <label for="code">Coupon Code:</label>
      <input type="text" id="code" name="code" required>
    </div>
    <div>
      <label for="description">Description:</label>
      <textarea id="description" name="description" rows="3" required></textarea>
    </div>
    <div>
      <label for="discount_type">Discount Type:</label>
      <select id="discount_type" name="discount_type" required>
        <option value="percentage">Percentage</option>
        <option value="fixed">Fixed Amount</option>
      </select>
    </div>
    <div>
      <label for="discount_value">Discount Value:</label>
      <input type="number" id="discount_value" name="discount_value" step="0.01" min="0" required>
    </div>
    <div>
      <label for="min_purchase">Minimum Purchase:</label>
      <input type="number" id="min_purchase" name="min_purchase" step="0.01" min="0" value="0">
    </div>
    <div>
      <label for="start_date">Start Date:</label>
      <input type="date" id="start_date" name="start_date" required>
    </div>
    <div>
      <label for="end_date">End Date:</label>
      <input type="date" id="end_date" name="end_date" required>
    </div>
    <div>
      <label for="max_uses">Maximum Uses:</label>
      <input type="number" id="max_uses" name="max_uses" min="0" value="0">
      <small>0 = unlimited</small>
    </div>
    <div>
      <label for="status">Status:</label>
      <select id="status" name="status" required>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="expired">Expired</option>
      </select>
    </div>
    <div>
      <button type="submit">Save Coupon</button>
      <button type="button" id="cancel-add">Cancel</button>
    </div>
  </form>
</div>

<div id="edit-coupon-form" style="display: none;">
  <h3>Edit Coupon</h3>
  <form id="editCouponForm" method="post" action="actions/edit_coupon.php">
    <input type="hidden" id="edit_id" name="id">
    <div>
      <label for="edit_code">Coupon Code:</label>
      <input type="text" id="edit_code" name="code" required>
    </div>
    <div>
      <label for="edit_description">Description:</label>
      <textarea id="edit_description" name="description" rows="3" required></textarea>
    </div>
    <div>
      <label for="edit_discount_type">Discount Type:</label>
      <select id="edit_discount_type" name="discount_type" required>
        <option value="percentage">Percentage</option>
        <option value="fixed">Fixed Amount</option>
      </select>
    </div>
    <div>
      <label for="edit_discount_value">Discount Value:</label>
      <input type="number" id="edit_discount_value" name="discount_value" step="0.01" min="0" required>
    </div>
    <div>
      <label for="edit_min_purchase">Minimum Purchase:</label>
      <input type="number" id="edit_min_purchase" name="min_purchase" step="0.01" min="0">
    </div>
    <div>
      <label for="edit_start_date">Start Date:</label>
      <input type="date" id="edit_start_date" name="start_date" required>
    </div>
    <div>
      <label for="edit_end_date">End Date:</label>
      <input type="date" id="edit_end_date" name="end_date" required>
    </div>
    <div>
      <label for="edit_max_uses">Maximum Uses:</label>
      <input type="number" id="edit_max_uses" name="max_uses" min="0">
      <small>0 = unlimited</small>
    </div>
    <div>
      <label for="edit_status">Status:</label>
      <select id="edit_status" name="status" required>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="expired">Expired</option>
      </select>
    </div>
    <div>
      <button type="submit">Update Coupon</button>
      <button type="button" id="cancel-edit">Cancel</button>
    </div>
  </form>
</div>

<table id="coupons-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Code</th>
      <th>Description</th>
      <th>Discount</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Uses</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT c.*, COUNT(bc.id) as used_count 
                FROM coupons c 
                LEFT JOIN booking_coupons bc ON c.id = bc.coupon_id 
                GROUP BY c.id 
                ORDER BY c.created_at DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // Format discount
        $discount = '';
        if ($row['discount_type'] == 'percentage') {
          $discount = $row['discount_value'] . '%';
        } else {
          $discount = '$' . number_format($row['discount_value'], 2);
        }

        // Check max uses
        $usesInfo = $row['used_count'] . '/' . ($row['max_uses'] > 0 ? $row['max_uses'] : '∞');

        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . $row['code'] . "</strong></td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td>" . $discount . "</td>";
        echo "<td>" . date('M d, Y', strtotime($row['start_date'])) . "</td>";
        echo "<td>" . date('M d, Y', strtotime($row['end_date'])) . "</td>";
        echo "<td>" . $usesInfo . "</td>";
        echo "<td>" . ucfirst($row['status']) . "</td>";
        echo "<td>
                        <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                        <button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='9'>No coupons found</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add new coupon
    document.getElementById('add-coupon-btn').addEventListener('click', function() {
      document.getElementById('add-coupon-form').style.display = 'block';

      // Set default dates
      const today = new Date();
      const nextMonth = new Date();
      nextMonth.setMonth(nextMonth.getMonth() + 1);

      document.getElementById('start_date').value = today.toISOString().split('T')[0];
      document.getElementById('end_date').value = nextMonth.toISOString().split('T')[0];
    });

    document.getElementById('cancel-add').addEventListener('click', function() {
      document.getElementById('add-coupon-form').style.display = 'none';
    });

    // Edit coupon
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const couponId = this.getAttribute('data-id');
        fetchCouponDetails(couponId);
      });
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
      document.getElementById('edit-coupon-form').style.display = 'none';
    });

    // Delete coupon
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this coupon?')) {
          const couponId = this.getAttribute('data-id');
          deleteCoupon(couponId);
        }
      });
    });

    // Form validations
    document.getElementById('couponForm').addEventListener('submit', function(e) {
      e.preventDefault();
      validateAndSubmit(this);
    });

    document.getElementById('editCouponForm').addEventListener('submit', function(e) {
      e.preventDefault();
      validateAndSubmit(this);
    });

    function validateAndSubmit(form) {
      // Get form fields
      const discountType = form.querySelector('[name="discount_type"]').value;
      const discountValue = parseFloat(form.querySelector('[name="discount_value"]').value);
      const startDate = new Date(form.querySelector('[name="start_date"]').value);
      const endDate = new Date(form.querySelector('[name="end_date"]').value);

      // Validate percentage discount
      if (discountType === 'percentage' && (discountValue <= 0 || discountValue > 100)) {
        alert('Percentage discount must be between 0 and 100');
        return false;
      }

      // Validate fixed discount
      if (discountType === 'fixed' && discountValue <= 0) {
        alert('Fixed discount must be greater than 0');
        return false;
      }

      // Validate dates
      if (startDate >= endDate) {
        alert('End date must be after start date');
        return false;
      }

      // Submit the form
      const formData = new FormData(form);

      fetch(form.action, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message || 'Coupon saved successfully!');
            window.location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while saving the coupon.');
        });
    }
  });

  function fetchCouponDetails(id) {
    fetch('actions/get_coupon.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('edit_id').value = data.coupon.id;
          document.getElementById('edit_code').value = data.coupon.code;
          document.getElementById('edit_description').value = data.coupon.description;
          document.getElementById('edit_discount_type').value = data.coupon.discount_type;
          document.getElementById('edit_discount_value').value = data.coupon.discount_value;
          document.getElementById('edit_min_purchase').value = data.coupon.min_purchase;
          document.getElementById('edit_start_date').value = data.coupon.start_date;
          document.getElementById('edit_end_date').value = data.coupon.end_date;
          document.getElementById('edit_max_uses').value = data.coupon.max_uses;
          document.getElementById('edit_status').value = data.coupon.status;

          document.getElementById('edit-coupon-form').style.display = 'block';
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while fetching coupon details.');
      });
  }

  function deleteCoupon(id) {
    fetch('actions/delete_coupon.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Coupon deleted successfully!');
          const row = document.querySelector(`tr[data-id="${id}"]`);
          if (row) {
            row.remove();
          } else {
            window.location.reload();
          }
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the coupon.');
      });
  }
</script>

<?php include 'includes/footer.php'; ?>