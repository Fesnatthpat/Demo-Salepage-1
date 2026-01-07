<?php

namespace App\Services;

class PromptPayService
{
    /**
     * Generate a PromptPay QR Code payload.
     *
     * @param string $mobile The PromptPay target mobile number.
     * @param float $amount The amount to be paid.
     * @return string The generated PromptPay payload.
     */
    public function generatePayload(string $mobile, float $amount): string
    {
        // Format mobile number to 0066...
        $formattedMobile = (strlen($mobile) == 10 && str_starts_with($mobile, "0")) ? "0066" . substr($mobile, 1) : $mobile;

        // Format amount to 2 decimal places
        $amountStr = number_format($amount, 2, '.', '');
        $amountLen = str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT);

        // Construct the payload string
        $payload = "00020101021229370016A0000006770101110113{$formattedMobile}5802TH530376454{$amountLen}{$amountStr}6304";
        
        // Append CRC16 checksum
        return $payload . $this->crc16($payload);
    }

    /**
     * Calculate CRC16 checksum for the payload.
     *
     * @param string $payload
     * @return string
     */
    private function crc16(string $payload): string
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($payload); $i++) {
            $crc ^= (ord($payload[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
            }
        }
        $crc &= 0xFFFF;
        
        return strtoupper(str_pad(dechex($crc), 4, "0", STR_PAD_LEFT));
    }
}
