<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        try {
            // Validate the input data
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'amount' => 'required|numeric', // Ensure amount is validated
                'method' => 'required|string|in:credit_card,bank_transfer,paypal', // Restrict allowed methods
            ]);
        } catch (ValidationException $e) {
            // Catch validation errors and return as JSON response
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        }

        try {
            // Fetch the order based on validated order_id
            $order = Order::find($validated['order_id']);
            $user = $order->user; // Assuming the order is linked to a user

            // Initialize amount paid and remaining due
            $amountPaid = $validated['amount']; // Use the amount provided in the request
            $remainingDue = max(0, $order->total_amount - $amountPaid); // Calculate remaining due

            // Determine if the payment can be accepted based on user type and payment terms
            if (!$user->isCommercial) {
                // Non-commercial user: Full payment required
                if ($amountPaid < $order->total_amount) {
                    return response()->json(['error' => 'Full payment is required for non-commercial users.'], 400);
                }
                $order->status = 'completed'; // Status for completed payment
            } else {
                // For commercial users, update order status based on payment terms
                if ($order->payment_terms === 'fullpayment' && $amountPaid < $order->total_amount) {
                    return response()->json(['error' => 'Full payment is required for this payment term.'], 400);
                }
                // Update order status based on the amount paid
                if ($amountPaid >= $order->total_amount) {
                    $order->status = 'completed'; // Full payment made
                } else {
                    $order->status = 'processing'; // Partial payment
                }
            }

            // Create a payment record using the validated data
            $payment = Payment::create([
                'order_id' => $order->id, // Associate with the correct order
                'user_id' => $order->user_id, // Associate payment with the user who placed the order
                'total_amount' => $order->total_amount, // Total order amount
                'amount_paid' => $amountPaid, // Amount paid from the request
                'remaining_due' => $remainingDue, // Calculate remaining due
                'method' => $validated['method'], // The payment method
            ]);

            // Save the updated order
            $order->save();

            // Create an invoice after the payment
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'due_date' => now()->addDays(30), // Set due date to 30 days after payment
                'total_amount' => $order->total_amount,
                'amount_paid' => $amountPaid, // Amount paid
                'remaining_due' => $remainingDue, // Remaining amount
                'status' => ($remainingDue > 0) ? 'pending' : 'paid', // Invoice status based on remaining due
            ]);

            // Return the payment, updated order, and invoice details
            return response()->json([
                'payment' => $payment,
                'order' => $order,
                'invoice' => $invoice
            ], 200);

        } catch (\Exception $e) {
            // Catch any errors and return a failure response
            return response()->json(['error' => 'Payment processing failed', 'message' => $e->getMessage()], 500);
        }
    }
}
