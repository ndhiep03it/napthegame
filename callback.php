<?php
$data = json_decode(file_get_contents("php://input"), true);

if ($data['status'] == 1) { // thành công
    $amount = $data['value']; // số tiền nạp
    $note = $data['note']; // chính là PlayFabId nếu đã gửi từ trước

    // Gọi PlayFab CloudScript cộng tiền
    $playfabId = $note;
    $currencyAmount = (int)$amount;

    $cloudScriptUrl = "https://<YOUR_TITLE_ID>.playfabapi.com/Server/ExecuteCloudScript";
    $secretKey = "YOUR_SECRET_KEY";

    $payload = [
        "FunctionName" => "RechargeFromCard",
        "FunctionParameter" => [
            "amount" => $currencyAmount,
            "playFabId" => $playfabId
        ]
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
}
