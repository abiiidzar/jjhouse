<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function editGeneral()
    {
        return view('admin.settings.general');
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string',
            'store_description' => 'nullable|string',
            'store_email' => 'required|email',
            'store_phone' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            cache()->put('setting_' . $key, $value);
        }

        return back()->with('success', 'General settings updated successfully!');
    }

    public function editPayment()
    {
        return view('admin.settings.payment');
    }

    public function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'midtrans_server_key' => 'required|string',
            'midtrans_client_key' => 'required|string',
            'midtrans_environment' => 'required|in:production,sandbox',
        ]);

        foreach ($validated as $key => $value) {
            cache()->put('payment_' . $key, $value);
        }

        return back()->with('success', 'Payment settings updated successfully!');
    }

    public function editShipping()
    {
        return view('admin.settings.shipping');
    }

    public function updateShipping(Request $request)
    {
        $validated = $request->validate([
            'rajaongkir_api_key' => 'required|string',
            'default_origin_city' => 'required|integer',
            'free_shipping_threshold' => 'required|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            cache()->put('shipping_' . $key, $value);
        }

        return back()->with('success', 'Shipping settings updated successfully!');
    }
}
