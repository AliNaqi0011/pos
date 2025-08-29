<?php



namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SalePaymentController extends Controller
{
    // Ensure only authenticated users can access sale payments
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show the form to add payment for a sale
    public function create($saleId)
    {
        $sale = Sale::findOrFail($saleId);
        return view('admin.sales.payments.create', compact('sale'));
    }

    // Store a new payment for the sale
    public function store(Request $request, $saleId)
    {
        // Validate the incoming request
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Find the sale by ID
            $sale = Sale::findOrFail($saleId);

            // Ensure the payment doesn't exceed the remaining balance
            $remainingAmount = $sale->final_total - $sale->paid_amount;
            if ($request->amount > $remainingAmount) {
                return back()->withErrors(['error' => 'Payment exceeds remaining balance!']);
            }

            // Create SalePayment record
            $payment = new SalePayment([
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'notes' => $request->notes,
            ]);

            // Associate the payment with the sale
            $payment->sale()->associate($sale);
            $payment->save();

            // Update the sale's paid amount
            $sale->paid_amount += $request->amount;
            $sale->save();

            // If the sale is fully paid, mark it as completed
            if ($sale->paid_amount >= $sale->final_total) {
                $sale->payment_status = 'paid';
                $sale->status = 'completed';
                $sale->save();
            }

            DB::commit();
            return redirect()->route('admin.sales.show', $saleId)->with('success', 'Payment successfully added!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    // Optional: Show the list of payments made for a sale
    public function showPayments($saleId)
    {
        $sale = Sale::findOrFail($saleId);
        $payments = $sale->payments; // Assuming relationship is defined in Sale model
        return view('admin.sales.payments.index', compact('sale', 'payments'));
    }
}
