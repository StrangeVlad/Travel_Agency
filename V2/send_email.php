<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hotel = isset($_POST['hotel']) ? $_POST['hotel'] : '';
    $checkin = isset($_POST['checkin']) ? $_POST['checkin'] : '';
    $checkout = isset($_POST['checkout']) ? $_POST['checkout'] : '';
    $guests = isset($_POST['guests']) ? $_POST['guests'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $payment = isset($_POST['payment']) ? $_POST['payment'] : '';

    if (empty($hotel) 
    || empty($checkin) 
|| empty($checkout) 
|| empty($guests) 
|| empty($email) 
|| empty($payment)) {
        echo "error: missing fields";
        exit();
    }

    // إرسال الإيميل
    $to = $email;
    $subject = "Confirmation de réservation pour $hotel";
    $message = "Bonjour,\n\nVotre réservation à $hotel est confirmée.\n\n";
    $message .= "📅 Check-in: 
    $checkin\n📅 Check-out: 
    $checkout\n👤 Nombre de personnes: 
    $guests\n💳 Mode de paiement: 
    $payment\n\nMerci pour votre réservation !";
    $headers = "From: agence@votre-site.com";

    if (mail($to, $subject, $message, $headers)) {
        echo "success";
    } else {
        echo "error: mail sending failed";
    }
} else {
    echo "error: invalid request";
}
?>
