<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User; // Ensure you import the User model

class OrderController extends Controller
{
    public function placeOrder(Request $request)
{
    // Validate the order details
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'total_amount' => 'required|numeric',
        'payment_terms' => 'required|string',
    ]);

    // Fetch the user to check if they are commercial
    $user = User::find($validated['user_id']);

    // Check if the user is commercial and set valid payment terms accordingly
    if ($user->isCommercial) {
        // Allow both partial payment and full payment for commercial users
        $allowedPaymentTerms = ['fullpayment', 'partial_payment'];
    } else {
        // Only allow full payment for normal users
        $allowedPaymentTerms = ['fullpayment'];
    }

    // Validate the payment_terms against allowed options
    if (!in_array($validated['payment_terms'], $allowedPaymentTerms)) {
        return response()->json([
            'error' => 'Invalid payment term for the selected user type.'
        ], 400);
    }

    // Create the order
    $order = Order::create([
        'user_id' => $validated['user_id'],
        'total_amount' => $validated['total_amount'],
        'payment_terms' => $validated['payment_terms'],
        'status' => 'pending', // Or any default status you want
    ]);

    // Process the payment
    $paymentSuccessful = $this->processPayment($order);

    // Update order status based on payment success
    try {
        if ($paymentSuccessful) {
            $order->update(['status' => 'completed']);
            \Log::info("Order ID {$order->id} status updated to completed.");
        } else {
            $order->update(['status' => 'failed']);
            \Log::info("Order ID {$order->id} status updated to failed.");
        }
    } catch (\Exception $e) {
        \Log::error("Failed to update order ID {$order->id}: " . $e->getMessage());
    }

    // Return the order details
    return response()->json(['order' => $order], 201);
}


    private function processPayment(Order $order)
    {
        // Implement your payment processing logic here.
        // Return true if payment is successful, false otherwise.
        return true; // Assuming payment is successful for demonstration purposes
    }
}
