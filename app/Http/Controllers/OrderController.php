<?php
// app/Http/Controllers/Api/OrderController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index()
    {
        return response()->json(
            Order::with(['customer', 'service'])
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'berat' => 'required|numeric|min:0.1',
            'catatan' => 'nullable|string'
        ]);

        $service = Service::find($validated['service_id']);
        $totalHarga = $service->harga_per_kg * $validated['berat'];
        $estimatedCompletion = Carbon::now()->addHours($service->duration_hours);

        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'service_id' => $validated['service_id'],
            'berat' => $validated['berat'],
            'total_harga' => $totalHarga,
            'perkiraan_selesai' => $estimatedCompletion,
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json($order->load(['customer', 'service']), 201);
    }

    public function show(Order $order)
    {
        return response()->json($order->load(['customer', 'service']));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,ready,completed,canceled'
        ]);

        $order->update(['status' => $validated['status']]);
        return response()->json($order->load(['customer', 'service']));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(null, 204);
    }

    /**
     * Track order progress
     */
    public function trackOrder(Order $order)
    {
        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'perkiraan_selesai' => $order->perkiraan_selesai,
            'customer' => $order->customer,
            'service' => $order->service,
        ]);
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Order $order)
    {
        if (in_array($order->status, ['completed', 'canceled'])) {
            return response()->json([
                'message' => 'Order tidak bisa dibatalkan.'
            ], 400);
        }

        $order->update(['status' => 'canceled']);
        return response()->json($order->load(['customer', 'service']));
    }
}
