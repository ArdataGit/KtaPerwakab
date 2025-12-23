<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MembershipFeeApiController extends Controller
{
    /**
     * Submit iuran (CREATE membership_fee via API)
     */
    public function submit(Request $request)
    {
        // Tentukan nominal
        if ($request->type === 'fixed') {
            $amount = 200000;
        } else {
            $request->validate([
                'nominal_custom' => 'required|numeric|min:10000',
            ]);
            $amount = $request->nominal_custom;
        }

        $user = session('user');

        dd(session('user'));

        if (!$user || empty($user['token'])) {
            abort(401, 'Unauthorized');
        }

        $response = Http::withToken($user['token'])
            ->post(config('services.kta_api.base_url') . '/membership-fee', [
                'amount' => $amount,
                'type' => 'tahunan',
                'payment_method' => 'manual',
            ]);

        if (!$response->successful()) {
            return back()->withErrors(
                $response->json('message') ?? 'Gagal membuat iuran'
            );
        }

        $fee = $response->json('data');

        session([
            'membership_fee_id' => $fee['id'],
            'membership_fee_amount' => $amount,
        ]);

        return redirect()->route('mobile.iuran.metode');
    }

    /**
     * Halaman informasi rekening
     */
    public function metode()
    {
        return view('livewire.mobile.iuran-metode', [
            'amount' => session('membership_fee_amount'),
        ]);
    }

    /**
     * Upload bukti pembayaran (CALL API)
     */
    public function uploadProof(Request $request)
    {
        $request->validate([
            'proof_image' => 'required|image|max:2048',
        ]);

        $user = session('user');
        $feeId = session('membership_fee_id');

        if (!$user || !$feeId) {
            abort(400, 'Data iuran tidak ditemukan');
        }

        $response = Http::withToken($user['token'])
            ->attach(
                'proof_image',
                fopen($request->file('proof_image')->getRealPath(), 'r'),
                $request->file('proof_image')->getClientOriginalName()
            )
            ->post(
                config('services.kta_api.base_url')
                . "/membership-fee/{$feeId}/upload-proof"
            );

        if (!$response->successful()) {
            return back()->withErrors('Upload bukti pembayaran gagal');
        }

        return redirect()->route('mobile.home')
            ->with('success', 'Bukti pembayaran berhasil dikirim');
    }
}
