<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Models\Order;
use Illuminate\Support\Facades\Log;



class LaundryStatusNotification extends Notification
{
    protected Order $order;
    protected string $status;

    public function __construct(Order $order, string $status)
    {
        $this->order  = $order;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $map = [
            'selesai' => [
                'title'   => 'Laundry Selesai',
                'message' => 'Laundry Anda sudah selesai, silakan ambil cucian Anda.',
            ],
            'diterima' => [
                'title'   => 'Order Diterima',
                'message' => 'Order laundry Anda telah diterima oleh mitra.',
            ],
            'ditolak' => [
                'title'   => 'Order Ditolak',
                'message' => 'Maaf, order laundry Anda ditolak oleh mitra.',
            ],
        ];

        $data = $map[$this->status] ?? [
            'title'   => 'Update Order',
            'message' => 'Status order Anda telah diperbarui.',
        ];

        return [
            'title'      => $data['title'],
            'message'    => $data['message'],
            'status'     => $this->status,
            'order_id'   => $this->order->id,
            'kode_order' => $this->order->kode_order,
        ];
    }
}


// class LaundrySelesaiNotification extends Notification
// {
//     protected Order $order;

//     public function __construct(Order $order)
//     {
//         $this->order = $order;
//         Log::info('LaundrySelesaiNotification created for Order ID: ' . $order->id);
//     }

//     public function via($notifiable)
//     {
//         return ['database'];
//     }

//     public function toArray($notifiable)
//     {
//         return [
//             'title'      => 'Laundry Selesai',
//             'message'    => 'Laundry Anda sudah selesai, silakan ambil cucian Anda.',
//             'order_id'   => $this->order->id,
//             'kode_order' => $this->order->kode_order,
//         ];
//     }
// }
