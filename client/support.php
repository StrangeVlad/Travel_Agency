<?php
include 'includes/header.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
?>

<section class="support-section">
  <h1>Contact Support</h1>

  <div class="support-info">
    <p>Need help with your booking or have questions about our travel packages? Our support team is here to help!</p>

    <div class="contact-methods">
      <div class="contact-method">
        <h3>Phone</h3>
        <p>Call us at: <strong>+1-800-123-4567</strong></p>
        <p>Hours: Monday - Friday, 9:00 AM - 5:00 PM EST</p>
      </div>

      <div class="contact-method">
        <h3>Email</h3>
        <p>Send us an email at: <strong>support@travelagency.com</strong></p>
        <p>We typically respond within 24 hours.</p>
      </div>
    </div>
  </div>

  <div class="support-form-container">
    <h2>Submit a Support Ticket</h2>

    <?php if ($is_logged_in): ?>
      <div id="success-message" class="success-message" style="display: none;"></div>
      <div id="error-message" class="error-message" style="display: none;"></div>

      <form id="support-form" method="post" action="actions/create_ticket.php">
        <div class="form-group">
          <label for="subject">Subject</label>
          <input type="text" id="subject" name="subject" required>
        </div>

        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" rows="5" required></textarea>
        </div>

        <button type="submit" id="submit-ticket">Submit Ticket</button>
      </form>

      <script>
        document.getElementById('support-form').addEventListener('submit', function(e) {
          e.preventDefault();

          const subject = document.getElementById('subject').value;
          const message = document.getElementById('message').value;
          const successMessage = document.getElementById('success-message');
          const errorMessage = document.getElementById('error-message');
          const submitButton = document.getElementById('submit-ticket');

          // Disable button to prevent multiple submissions
          submitButton.disabled = true;
          submitButton.textContent = 'Submitting...';

          // AJAX request
          const xhr = new XMLHttpRequest();
          xhr.open('POST', 'actions/create_ticket.php', true);
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

          xhr.onload = function() {
            if (this.status === 200) {
              const response = JSON.parse(this.responseText);

              if (response.success) {
                // Show success message
                successMessage.textContent = response.message;
                successMessage.style.display = 'block';
                errorMessage.style.display = 'none';

                // Clear form
                document.getElementById('subject').value = '';
                document.getElementById('message').value = '';
              } else {
                // Show error message
                errorMessage.textContent = response.message;
                errorMessage.style.display = 'block';
                successMessage.style.display = 'none';
              }

              submitButton.disabled = false;
              submitButton.textContent = 'Submit Ticket';
            } else {
              errorMessage.textContent = 'An error occurred. Please try again.';
              errorMessage.style.display = 'block';
              successMessage.style.display = 'none';
              submitButton.disabled = false;
              submitButton.textContent = 'Submit Ticket';
            }
          };

          xhr.send('subject=' + encodeURIComponent(subject) + '&message=' + encodeURIComponent(message));
        });
      </script>
    <?php else: ?>
      <div class="login-required">
        <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to submit a support ticket.</p>
      </div>
    <?php endif; ?>
  </div>

  <div class="faq-section">
    <h2>Frequently Asked Questions</h2>

    <div class="faq-item">
      <h3>How do I cancel my booking?</h3>
      <div class="faq-answer">
        <p>You can cancel your booking by going to "My Bookings" in your account dashboard and clicking the "Cancel Booking" button. Please note our cancellation policy: cancellations made more than 30 days before travel date receive a full refund, while cancellations within 30 days may be subject to fees.</p>
      </div>
    </div>

    <div class="faq-item">
      <h3>What payment methods do you accept?</h3>
      <div class="faq-answer">
        <p>We accept credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers. Payment details can be provided during the booking process.</p>
      </div>
    </div>

    <div class="faq-item">
      <h3>Can I modify my booking after it's confirmed?</h3>
      <div class="faq-answer">
        <p>Yes, modifications can be made depending on availability. Please contact our support team with your booking ID for assistance with any changes.</p>
      </div>
    </div>

    <div class="faq-item">
      <h3>Do I need travel insurance?</h3>
      <div class="faq-answer">
        <p>While not mandatory, we strongly recommend purchasing travel insurance to protect against unforeseen circumstances. We can provide recommendations for reliable travel insurance providers.</p>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
  document.querySelectorAll('.faq-item h3').forEach(header => {
    header.addEventListener('click', () => {
      const item = header.parentElement;
      item.classList.toggle('open');
    });
  });
</script>