<?php
function thongbao($msg)
{
    echo '<script>window.alert("' . addslashes($msg) . '");</script>';
}

function curl_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // tránh treo quá lâu
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

if (isset($_POST['napthe'])) {
    $loaithe    = $_POST['loaithe'] ?? '';
    $menhgia    = $_POST['menhgia'] ?? '';
    $seri       = $_POST['seri'] ?? '';
    $mathe      = $_POST['mathe'] ?? '';
    $playfab_id = $_POST['playfab_id'] ?? '';

    // Kiểm tra thông tin đầu vào
    if (!$loaithe || !$menhgia || !$seri || !$mathe || !$playfab_id) {
        thongbao("Bạn chưa nhập đủ thông tin");
        return;
    }

    if (!ctype_digit($seri)) {
        thongbao("Seri phải là số và không chứa ký tự đặc biệt");
        return;
    }

    if (!ctype_digit($mathe)) {
        thongbao("Mã thẻ phải là số và không chứa ký tự đặc biệt");
        return;
    }

    // Cấu hình API Thesieure
    $partner_id = 'YOUR_PARTNER_ID'; // ✅ Thay bằng ID thật
    $partner_key = 'YOUR_PARTNER_KEY'; // ✅ Thay bằng KEY thật
    $request_id = time() . rand(1000, 9999); // ID ngẫu nhiên
    $sign = md5($partner_key . $mathe . $seri); // SHA bảo mật

    // Tạo URL nạp thẻ
    $url = "https://thesieure.com/chargingws/v2?"
        . "sign=$sign"
        . "&telco=$loaithe"
        . "&code=$mathe"
        . "&serial=$seri"
        . "&amount=$menhgia"
        . "&request_id=$request_id"
        . "&partner_id=$partner_id"
        . "&command=charging"
        . "&note=" . urlencode($playfab_id);

    // Gửi yêu cầu nạp
    $data = curl_get($url);
    $json = json_decode($data, true);

    // Phản hồi người dùng
    if (isset($json['status']) && $json['status'] == 99) {
        thongbao("🎉 Gửi thẻ thành công, đang chờ xác nhận từ Thesieure...");
    } else {
        thongbao("❌ Lỗi gửi thẻ: " . ($json['message'] ?? "Không xác định"));
    }
}
?>
