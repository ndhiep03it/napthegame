<?php
// Nhận JSON từ Thesieure
$data = json_decode(file_get_contents("php://input"), true);

// Chỉ xử lý nếu thẻ nạp thành công
if ($data && $data['status'] == 1) {
    $amount_raw = (int)$data['amount']; // mệnh giá gốc
    $amount_real = (int)$data['value']; // mệnh giá được duyệt
    $playfabId = $data['note']; // chính là PlayFabId từ form

    // ⚖️ Quy đổi mệnh giá sang tiền game
    $gameCoin = 0;
    switch ($amount_raw) {
        case 10000: $gameCoin = 100; break;
        case 20000: $gameCoin = 210; break;
        case 50000: $gameCoin = 550; break;
        case 100000: $gameCoin = 1200; break;
        case 200000: $gameCoin = 2500; break;
        case 500000: $gameCoin = 7000; break;
        case 1000000: $gameCoin = 15000; break;
        default: $gameCoin = (int)($amount_raw / 100); break;
    }

    // 📡 Gọi CloudScript PlayFab để cộng tiền
    $cloudScriptUrl = "https://5A23B.playfabapi.com/Server/ExecuteCloudScript";
    $secretKey = "FGUBFFXGWSM4DHNMW7AIZZ8UOOMYI4UCKDXAH7WQTK5WQHGAIA"; // Server Secret Key

    $payload = [
        "FunctionName" => "RechargeFromCard",
        "FunctionParameter" => [
            "amount" => $gameCoin,
            "napmenhgia" => $amount_raw
        ],
        "PlayFabId" => $playfabId, // BẮT BUỘC
        "GeneratePlayStreamEvent" => true
    ];

    $headers = [
        "Content-Type: application/json",
        "X-SecretKey: $secretKey"
    ];

    $ch = curl_init($cloudScriptUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    curl_close($ch);

    // 📝 Ghi log
    file_put_contents("log_napthe.txt", "[".date("Y-m-d H:i:s")."] "
        . "PlayFabId: $playfabId | Mệnh giá: $amount_raw | Coin: $gameCoin | API Response: $res\n", FILE_APPEND);
}
?>
