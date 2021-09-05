<?php
$merchant_id            = !empty($_POST['merchant_id']) ? $_POST['merchant_id'] : '00281672800072990000000081060002';
$merchant_category      = !empty($_POST['merchant_category']) ? $_POST['merchant_category'] : '7299';
$merchant_name          = !empty($_POST['merchant_name']) ? $_POST['merchant_name'] : '';
$merchant_city          = !empty($_POST['merchant_city']) ? $_POST['merchant_city'] : '';
$merchant_postal_code   = !empty($_POST['merchant_postal_code']) ? $_POST['merchant_postal_code'] : '';
$amount                 = !empty($_POST['amount']) ? $_POST['amount'] : '';
$payment_type           = !empty($_POST['payment_type']) ? true : false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $qr_string = get_lankaqr_string($merchant_id, $merchant_category, $merchant_name, $merchant_city, $merchant_postal_code, $amount, $payment_type);
    //load the qr library
    include 'phpqrcode/qrlib.php';
    $QR = generate_qr_code($qr_string);
}
function get_lankaqr_string($merchant_id = null, $merchant_category = null, $merchant_name = null, $merchant_city = null, $merchant_postal_code = null, $amount = null, $payment_type = null)
{
    $string = '000201';
    $string .= $payment_type ? '010212' : '010211';
    $string .= '2632' . $merchant_id;
    $string .= '5204' . $merchant_category;
    $string .= '5303144';
    if (!empty($amount)) {
        $formated_amount = number_format($amount, 2, '.', '');
        $string .= '54' . get_str_length($formated_amount) . $formated_amount;
    }
    $string .= '5802LK';
    $string .= '59' . get_str_length($merchant_name) . $merchant_name;
    $string .= '60' . get_str_length($merchant_city) . $merchant_city;
    if (!empty($merchant_postal_code)) {
        $string .= '61' . get_str_length($merchant_postal_code) . $merchant_postal_code;
    }
    $string .= '6304';
    $string .= crcChecksum($string);
    return $string;
}
function get_str_length($string)
{
    $length = strlen($string);
    if ($length < 10) {
        $length = '0' . $length;
    }
    return $length;
}
function crcChecksum($str)
{
    // The PHP version of the JS str.charCodeAt(i)
    function charCodeAt($str, $i)
    {
        return ord(substr($str, $i, 1));
    }

    $crc = 0xFFFF;
    $strlen = strlen($str);
    for ($c = 0; $c < $strlen; $c++) {
        $crc ^= charCodeAt($str, $c) << 8;
        for ($i = 0; $i < 8; $i++) {
            if ($crc & 0x8000) {
                $crc = ($crc << 1) ^ 0x1021;
            } else {
                $crc = $crc << 1;
            }
        }
    }
    $hex = $crc & 0xFFFF;
    $hex = dechex($hex);
    $hex = strtoupper($hex);

    return $hex;
}
function generate_qr_code($text)
{
    //file path
    $file = "images/".date("ymdhs").rand(10,99).".png";

    //other parameters
    $ecc = 'H';
    $pixel_size = 20;
    $frame_size = 5;

    // Generates QR Code and Save as PNG
    QRcode::png($text, $file, $ecc, $pixel_size, $frame_size);

    return $file;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <title>LankaQR Generator</title>
</head>

<body>
    <div class="container">
        <h2 class="text-center mt-5 mb-5">LankaQR Generator</h2>
        <div class="row">
            <div class="col-12 col-md-6">
                <form class="form-group" method="post">
                    <div class="mb-3">
                        <label for="merchant_id" class="form-label">Merchant ID</label>
                        <input type="text" class="form-control" id="merchant_id" name="merchant_id" value="<?= $merchant_id ?>">
                    </div>
                    <div class="mb-3">
                        <label for="merchant_category" class="form-label">Merchant category</label>
                        <input type="text" class="form-control" id="merchant_category" name="merchant_category" value="<?= $merchant_category ?>">
                    </div>
                    <div class="mb-3">
                        <label for="merchant_name" class="form-label">Merchant name</label>
                        <input type="text" class="form-control" id="merchant_name" name="merchant_name" value="<?= $merchant_name ?>">
                    </div>
                    <div class="mb-3">
                        <label for="merchant_city" class="form-label">Merchant city</label>
                        <input type="text" class="form-control" id="merchant_city" name="merchant_city" value="<?= $merchant_city ?>">
                    </div>
                    <div class="mb-3">
                        <label for="merchant_postal_code" class="form-label">Merchant postal code</label>
                        <input type="text" class="form-control" id="merchant_postal_code" name="merchant_postal_code" value="<?= $merchant_postal_code ?>">
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" value="<?= $amount ?>" max="50000">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="payment_type" name="payment_type" <?= $payment_type ? 'checked' : '' ?>>
                        <label class="form-check-label" for="payment_type">One time payment</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="col-12 col-md-6">
                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'):?>
                    <img src="<?=$QR?>" alt="LankaQR" class="m-auto img-fluid">
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>