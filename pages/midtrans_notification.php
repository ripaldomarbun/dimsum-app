<?php
include '../config/database.php';

$notif = new Midtrans_Notification();

$transaction_status = $notif->transaction_status;
$order_id = $notif->order_id;

if ($transaction_status == 'capture') {
    $status = 'dikonfirmasi';
} elseif ($transaction_status == 'settlement') {
    $status = 'dikonfirmasi';
} elseif ($transaction_status == 'pending') {
    $status = 'menunggu';
} elseif ($transaction_status == 'deny') {
    $status = 'dibatalkan';
} elseif ($transaction_status == 'expire') {
    $status = 'dibatalkan';
} elseif ($transaction_status == 'cancel') {
    $status = 'dibatalkan';
} else {
    $status = 'menunggu';
}

$conn->query("UPDATE pesanan SET status = '$status' WHERE nomor_pesanan = '$order_id'");

class Midtrans_Notification {
    private $data;

    public function __construct() {
        $input = file_get_contents('php://input');
        $notification = json_decode($input);
        
        $serverKey = 'Mid-server-8vwQGgWIIJPwk2mOn9lrArVh';
        
        $signatureKey = hash('sha512', 
            $notification->order_id . $notification->status_code . $notification->gross_amount . $serverKey
        );
        
        if ($signatureKey != $notification->signature_key) {
            http_response_code(403);
            exit('Invalid signature');
        }
        
        $this->data = $notification;
    }

    public function __get($key) {
        return isset($this->data->$key) ? $this->data->$key : null;
    }
}
?>
