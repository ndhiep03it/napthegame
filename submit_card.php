<?php
function thongbao($msg)
{
    echo '<script>window.alert("' . $msg . '");</script>';
}

function curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

if (isset($_POST['napthe'])) {
    $loaithe    = $_POST['loaithe'];
    $menhgia    = $_POST['menhgia'];
    $seri       = $_POST['seri'];
    $mathe      = $_POST['mathe'];
    $playfab_id = $_POST['playfab_id'] ?? ''; // thêm biến playfab_id

    if (!$loaithe || !$menhgia || !$seri || !$mathe || !$playfab_id) {
        thongbao("Bạn chưa nhập đủ thông tin");
    }

    if (preg_match('/\D/', $seri)) {
        thongbao("Seri của bạn phải là số 100% và không có chữ");
    }

    if (preg_match('/\D/', $mathe)) {
        thongbao("Mã thẻ của bạn phải là số 100% và không có chữ");
    }

    // Cấu hình API Thesieure
    $partner_id = 'YOUR_PARTNER_ID'; // thay bằng ID của bạn
    $partner_key = 'YOUR_PARTNER_KEY'; // thay bằng KEY của bạn
    $ranid = rand(1111111111, 9999999999);
    $sign = md5($partner_key . $mathe . $seri);

    // Gửi yêu cầu nạp
    $url = "https://thesieure.com/chargingws/v2?sign=$sign"
        . "&telco=$loaithe&code=$mathe&serial=$seri"
        . "&amount=$menhgia&request_id=$ranid"
        . "&partner_id=$partner_id&command=charging"
        . "&note=$playfab_id"; // Gửi playfab_id theo dạng note để xử lý callback

    $data = curl_get($url);
    $json = json_decode($data, true);

    if ($json['status'] == 99) {
        thongbao("Gửi thẻ thành công, vui lòng chờ xác nhận...");
    } else {
        thongbao("Lỗi gửi thẻ: " . $json['message']);
    }
}
?>
