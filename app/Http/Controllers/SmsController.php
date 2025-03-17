<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class SmsController extends Controller
{
    /**
     * Send SMS to the given phone number
     *
     * @param string $phoneNumber
     * @param string $message
     * @return bool
     */
    public function sendSms($phoneNumber, $message)
    {
        try {
            // Get URL from .env (with default fallback)
            $url = env('SMS_GATEWAY_URL', 'http://192.168.178.98:8888/send-message');
            $username = env('SMS_GATEWAY_USERNAME', 'admin');
            $password = env('SMS_GATEWAY_PASSWORD', 'password');

            // Log attempt
            Log::info("Attempting to send SMS to: {$phoneNumber}");

            $jsonData = json_encode([
                'phoneNumber' => $phoneNumber,
                'message' => $message
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ]);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $info = curl_getinfo($ch);

            if (curl_errno($ch)) {
                Log::error("cURL Error: " . curl_error($ch));
            }

            curl_close($ch);

            $success = ($info['http_code'] >= 200 && $info['http_code'] < 300);

            return $success;
        } catch (\Exception $e) {
            Log::error('SMS Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send order confirmation SMS
     *
     * @param string $phoneNumber
     * @param string $tokenNumber
     * @param string $gasTypeName
     * @param string $outletName
     * @param int $quantity
     * @param float $totalAmount
     * @return bool
     */
    public function sendOrderConfirmation($phoneNumber, $tokenNumber, $gasTypeName, $outletName, $quantity, $totalAmount)
    {
        $message = "GasbyGas: Your order has been placed successfully! Token: " . $tokenNumber;
        $message .= " ,QTY - " . $quantity . ", Gas Type: " . $gasTypeName;
        $message .= ", Pickup at: " . $outletName;
        $message .= ", Amount: Rs. " . $totalAmount;

        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send verification code SMS
     *
     * @param string $phoneNumber
     * @param string $verificationCode
     * @return bool
     */
    public function sendVerificationCode($phoneNumber, $verificationCode)
    {
        $message = "🔹 GASBYGAS 🔹\nYour One-Time Password (OTP) is: $verificationCode";
        return $this->sendSms($phoneNumber, $message);
    }

}
