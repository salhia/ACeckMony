<?php

namespace App\Http\Controllers;

use App\Models\SysTransaction;
use App\Models\SysCustomer;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TransferVerificationController extends Controller
{
    public function showVerificationPage()
    {
        return view('transfers.verify');
    }

    public function verifyPhone(Request $request)
    {
        $phone = $request->input('phone');

        // First find the customer by phone number
        $customer = SysCustomer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['hasTransfers' => false]);
        }

        // Then check if this customer has any pending transfers as receiver
        $hasTransfers = SysTransaction::where('receiver_customer_id', $customer->id)
            ->where('status', 'pending')
            ->exists();

        return response()->json(['hasTransfers' => $hasTransfers]);
    }

    public function verifyQrCode(Request $request)
    {
        $code = $request->input('code');
        $phone = $request->input('phone');

        // First find the customer by phone number
        $customer = SysCustomer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid phone number'
            ]);
        }

        // Then find the transfer using both verification code and customer ID
        $transfer = SysTransaction::where('verification_code', $code)
            ->where('receiver_customer_id', $customer->id)
            ->where('status', 'pending')
            ->first();

        if (!$transfer) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired QR code'
            ]);
        }

        return response()->json([
            'valid' => true,
            'transfer' => [
                'id' => $transfer->id,
                'amount' => number_format($transfer->amount, 2),
                'status' => $transfer->status,
                'date' => $transfer->created_at->format('Y-m-d H:i:s')
            ]
        ]);
    }

    public function generateQrCode($transferId)
    {
        $transfer = SysTransaction::findOrFail($transferId);

        // Generate a unique verification code if not exists
        if (!$transfer->verification_code) {
            $transfer->verification_code = uniqid('TRF_');
            $transfer->save();
        }

        // Generate QR code
        $qrCode = QrCode::size(300)
            ->gradient(0, 123, 255, 0, 188, 212, 'diagonal')
            ->style('dot')
            ->eye('circle')
            ->format('png')
            ->generate($transfer->verification_code);

        return response($qrCode)->header('Content-Type', 'image/png');
    }
}
