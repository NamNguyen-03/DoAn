<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Tạo một instance mới.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Tiêu đề email và người gửi.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đơn hàng #' . $this->order->order_code
        );
    }

    /**
     * Nội dung của email (sử dụng view).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order',
            with: [
                'order' => $this->order
            ]
        );
    }

    /**
     * File đính kèm (nếu có).
     */
    public function attachments(): array
    {
        return [];
    }
}
