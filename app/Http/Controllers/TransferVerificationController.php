<?php

namespace App\Http\Controllers;

use App\Models\SysTransaction;
use App\Models\SysCustomer;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;

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

        // Get all transfers for this customer
        $transfers = SysTransaction::where('receiver_customer_id', $customer->id)
            ->with(['senderCustomer', 'receiverCustomer'])
            ->get();

        if ($transfers->isEmpty()) {
            return response()->json(['hasTransfers' => false]);
        }

        // Generate QR codes for each transfer
        $transfersData = $transfers->map(function ($transfer) {
            return [
                'id' => $transfer->id,
                'transaction_code' => $transfer->transaction_code,
                'amount' => number_format($transfer->amount, 2),
                'status' => $transfer->status,
                'date' => $transfer->created_at->format('Y-m-d H:i:s'),
                'sender_name' => $transfer->senderCustomer ? $transfer->senderCustomer->name : 'N/A',
                'receiver_name' => $transfer->receiverCustomer ? $transfer->receiverCustomer->name : 'N/A',
                'qr_code' => route('transfers.qr-code', $transfer->id),
                'pdf_url' => route('transfers.download.pdf', $transfer->id)
            ];
        });

        return response()->json([
            'hasTransfers' => true,
            'transfers' => $transfersData
        ]);
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
            ->first();

        if (!$transfer) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired QR code'
            ]);
        }

        // Get additional transfer details
        $senderCustomer = $transfer->senderCustomer;
        $receiverCustomer = $transfer->receiverCustomer;

        return response()->json([
            'valid' => true,
            'transfer' => [
                'id' => $transfer->id,
                'transaction_code' => $transfer->transaction_code,
                'amount' => number_format($transfer->amount, 2),
                'status' => $transfer->status,
                'date' => $transfer->created_at->format('Y-m-d H:i:s'),
                'sender_name' => $senderCustomer ? $senderCustomer->name : 'N/A',
                'receiver_name' => $receiverCustomer ? $receiverCustomer->name : 'N/A',
                'pdf_url' => route('transfers.download.pdf', $transfer->id)
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

        // Create QR code data with PDF download URL
        $qrData = [
            'verification_code' => $transfer->verification_code,
            'transaction_code' => $transfer->transaction_code,
            'amount' => $transfer->amount,
            'pdf_url' => route('transfers.download.pdf', $transfer->id)
        ];

        // Generate QR code
        $qrCode = QrCode::size(300)
            ->gradient(0, 123, 255, 0, 188, 212, 'diagonal')
            ->style('dot')
            ->eye('circle')
            ->format('png')
            ->generate(json_encode($qrData));

        return response($qrCode)->header('Content-Type', 'image/png');
    }

    public function downloadPdf($transferId)
    {
        $transfer = SysTransaction::with(['senderCustomer', 'receiverCustomer'])
            ->findOrFail($transferId);

        $data = [
            'transfer' => $transfer,
            'sender' => $transfer->senderCustomer,
            'receiver' => $transfer->receiverCustomer,
            'date' => $transfer->created_at->format('Y-m-d H:i:s'),
            'amount' => number_format($transfer->amount, 2)
        ];

        $pdf = PDF::loadView('transfers.pdf', $data);

        return $pdf->download('transfer_' . $transfer->transaction_code . '.pdf');
    }

    public function verifyWithCode($code)
    {
        $transfer = SysTransaction::where('verification_code', $code)
            ->with(['senderCustomer', 'receiverCustomer'])
            ->first();

        if (!$transfer) {
            return redirect()->route('transfers.verify')
                ->with('error', 'Invalid verification code. Please try again.');
        }

        return view('transfers.verify', [
            'directVerification' => true,
            'transfer' => [
                'id' => $transfer->id,
                'transaction_code' => $transfer->transaction_code,
                'amount' => number_format($transfer->amount, 2),
                'status' => $transfer->status,
                'date' => $transfer->created_at->format('Y-m-d H:i:s'),
                'sender_name' => $transfer->senderCustomer ? $transfer->senderCustomer->name : 'N/A',
                'receiver_name' => $transfer->receiverCustomer ? $transfer->receiverCustomer->name : 'N/A',
                'qr_code' => route('transfers.qr-code', $transfer->id),
                'pdf_url' => route('transfers.download.pdf', $transfer->id)
            ]
        ]);
    }

    public function generateVerificationUrlQr()
    {
        // Generate QR code for the verification URL
        $qrCode = QrCode::size(300)
            ->gradient(0, 123, 255, 0, 188, 212, 'diagonal')
            ->style('dot')
            ->eye('circle')
            ->format('png')
            ->generate('https://akec.money/verify');

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="akec-money-verify.png"');
    }
}
