<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ManualPayment;

class ManualPaymentController extends Controller
{
    public function index()
    {
        if(!request()->isXmlHttpRequest()) {
            return view('tailwind.admin.manual-payments.index');
        }

        $query = ManualPayment::query()
            ->with([
                'doctor:id,name,mobile_number,bmdc_no',
                'paymentable'
            ]);

        $this->search($query);
        $this->filter($query);

        // return
        $manual_payments = $query->latest()->paginate();

        return view('tailwind.admin.manual-payments.data', compact('manual_payments'))
            ->render();
    }

    protected function search(&$query)
    {
        $query
            ->when(request('search'), function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query
                        ->where('id', 'like', "%{$search}%")
                        ->orWhere('account_no', 'like', "%{$search}%")
                        ->orWhere('trx_id', 'like', "%{$search}%")
                        ->orWhereHas('doctor', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('mobile_number', 'like', "%{$search}%")
                                ->orWhere('bmdc_no', 'like', "%{$search}%");
                        });
                });
            });
    }

    protected function filter(&$query)
    {
        $query
            ->when(isset(request()->from), function ($query) {
                return $query->whereDate('updated_at', '>=', request('from'));
            })
            ->when(isset(request()->to), function ($query) {
                return $query->whereDate('updated_at', '<=', request('to'));
            });
    }
}
