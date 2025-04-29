<?php include 'includes/header.php'; ?>

<h2>Customer Reviews Management</h2>

<div class="action-buttons">
  <button id="add-review-btn">Add New Review</button>
</div>

<div id="add-review-form" style="display: none;">
  <h3>Add New Review</h3>
  <form id="reviewForm" method="post" action="actions/add_review.php">
    <div>
      <label for="customer_name">Customer Name:</label>
      <input type="text" id="customer_name" name="customer_name" required>
    </div>
    <div>
      <label for="hotel_id">Hotel:</label>
      <select id="hotel_id" name="hotel_id" required>
        <option value="">Select Hotel</option>
        <?php
        $sql = "SELECT id, name FROM hotels ORDER BY name ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
          }
        }
        ?>
      </select>
    </div>
    <div>
      <label for="rating">Rating (1-5):</label>
      <input type="number" id="rating" name="rating" min="1" max="5" required>
    </div>
    <div>
      <label for="review_text">Review Text:</label>
      <textarea id="review_text" name="review_text" required></textarea>
    </div>
    <div>
      <label for="review_date">Review Date:</label>
      <input type="date" id="review_date" name="review_date" required>
    </div>
    <div>
      <label for="status">Status:</label>
      <select id="status" name="status" required>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>
    <div>
      <button type="submit">Save Review</button>
      <button type="button" id="cancel-add">Cancel</button>
    </div>
  </form>
</div>

<div id="edit-review-form" style="display: none;">
  <h3>Edit Review</h3>
  <form id="editReviewForm" method="post" action="actions/edit_review.php">
    <input type="hidden" id="edit_id" name="id">
    <div>
      <label for="edit_customer_name">Customer Name:</label>
      <input type="text" id="edit_customer_name" name="customer_name" required>
    </div>
    <div>
      <label for="edit_hotel_id">Hotel:</label>
      <select id="edit_hotel_id" name="hotel_id" required>
        <option value="">Select Hotel</option>
        <?php
        $sql = "SELECT id, name FROM hotels ORDER BY name ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
          }
        }
        ?>
      </select>
    </div>
    <div>
      <label for="edit_rating">Rating (1-5):</label>
      <input type="number" id="edit_rating" name="rating" min="1" max="5" required>
    </div>
    <div>
      <label for="edit_review_text">Review Text:</label>
      <textarea id="edit_review_text" name="review_text" required></textarea>
    </div>
    <div>
      <label for="edit_review_date">Review Date:</label>
      <input type="date" id="edit_review_date" name="review_date" required>
    </div>
    <div>
      <label for="edit_status">Status:</label>
      <select id="edit_status" name="status" required>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>
    <div>
      <button type="submit">Update Review</button>
      <button type="button" id="cancel-edit">Cancel</button>
    </div>
  </form>
</div>

<table id="reviews-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Customer</th>
      <th>Hotel</th>
      <th>Rating</th>
      <th>Review</th>
      <th>Date</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $sql = "SELECT r.*, h.name as hotel_name 
                FROM reviews r 
                LEFT JOIN hotels h ON r.id = h.id 
                ORDER BY r.id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . $row['id'] . "'>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['customer_name'] . "</td>";
        echo "<td>" . $row['hotel_name'] . "</td>";
        echo "<td>" . $row['rating'] . "/5</td>";
        echo "<td>" . (strlen($row['review_text']) > 50 ? substr($row['review_text'], 0, 50) . "..." : $row['review_text']) . "</td>";
        echo "<td>" . $row['review_date'] . "</td>";
        echo "<td>" . ucfirst($row['status']) . "</td>";
        echo "<td>
                        <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                        <button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>
                      </td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='8'>No reviews found</td></tr>";
    }
    ?>
  </tbody>
</table>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add new review
    document.getElementById('add-review-btn').addEventListener('click', function() {
      // Set default date to today
      document.getElementById('review_date').value = new Date().toISOString().split('T')[0];
      document.getElementById('add-review-form').style.display = 'block';
    });

    document.getElementById('cancel-add').addEventListener('click', function() {
      document.getElementById('add-review-form').style.display = 'none';
    });

    // Edit review
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
      button.addEventListener('click', function() {
        const reviewId = this.getAttribute('data-id');
        fetchReviewDetails(reviewId);
      });
    });

    document.getElementById('cancel-edit').addEventListener('click', function() {
      document.getElementById('edit-review-form').style.display = 'none';
    });

    // Delete review
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this review?')) {
          const reviewId = this.getAttribute('data-id');
          deleteReview(reviewId);
        }
      });
    });

    // Form submissions
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('actions/add_review.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Review added successfully!');
            window.location.reload();
          } else {
            alert('Error adding review: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while adding the review.');
        });
    });

    document.getElementById('editReviewForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('actions/edit_review.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Review updated successfully!');
            window.location.reload();
          } else {
            alert('Error updating review: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while updating the review.');
        });
    });
  });

  function fetchReviewDetails(id) {
    fetch('actions/get_review.php?id=' + id)
      .then(response => response.json())
      .then(data => {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_customer_name').value = data.customer_name;
        document.getElementById('edit_hotel_id').value = data.hotel_id;
        document.getElementById('edit_rating').value = data.rating;
        document.getElementById('edit_review_text').value = data.review_text;
        document.getElementById('edit_review_date').value = data.review_date;
        document.getElementById('edit_status').value = data.status;

        document.getElementById('edit-review-form').style.display = 'block';
      })
      .catch(error => console.error('Error:', error));
  }

  function deleteReview(id) {
    fetch('actions/delete_review.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + id
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Remove the row from the table
          const row = document.querySelector(`tr[data-id="${id}"]`);
          row.parentNode.removeChild(row);
        } else {
          alert('Error deleting review: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the review.');
      });
  }
</script>

<?php include 'includes/footer.php'; ?>