<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReturnStatusCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->load('items.product');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = ucwords($this->order->return_status);
        return new Envelope(
            subject: "Your Return Request for Order #{$this->order->order_number} has been {$status}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.return-status-customer',
        );
    }
}
