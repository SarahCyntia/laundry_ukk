<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\LaundryStatusNotification;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order)
{
    // 1️⃣ BARANG BARU DIAMBIL (timestamp baru terisi)
    if ($order->isDirty('waktu_diambil') && $order->waktu_diambil) {
        // ❌ JANGAN KIRIM siap_diambil lagi
        // (opsional) kirim notif "Pesanan Diambil"
        return;
    }

    // 2️⃣ Laundry selesai → HANYA jika BELUM diambil
    if (
        $order->isDirty('status') &&
        $order->status === 'siap_diambil' &&
        $order->waktu_diambil === null
    ) {
        $order->user->notify(
            new LaundryStatusNotification($order, 'siap_diambil')
        );
    }

    // 3️⃣ Status lain
    if (
        $order->isDirty('status') &&
        in_array($order->status, ['diterima', 'ditolak'])
    ) {
        $order->user->notify(
            new LaundryStatusNotification($order, $order->status)
        );
    }
}


    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
