<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $isForAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $isForAdmin = false)
    {
        $this->order = $order->load('items.product');
        $this->isForAdmin = $isForAdmin;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = ucwords($this->order->order_status);
        $subject = $this->isForAdmin 
            ? "[Admin] Order #{$this->order->order_number} Status Updated to {$status}"
            : "Your Order #{$this->order->order_number} has been {$status}!";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-update',
        );
    }
}
