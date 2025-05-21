<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SendOTP extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;  // Thêm property OTP

    /**
     * Tạo instance mới với mã OTP.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;  // Gán OTP cho property
    }

    /**
     * Lấy thông tin envelope của email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mã xác nhận OTP',
        );
    }

    /**
     * Định nghĩa nội dung của email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp' => $this->otp,
            ],
        );
    }

    /**
     * Đính kèm tệp tin nếu cần.
     */
    public function attachments(): array
    {
        return [];
    }
}
