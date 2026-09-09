<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Customer;

class HomeController extends Controller
{
    public function index()
    {
        $dashboard = [
            'available' => false,
            'demo' => true,
            'todaySales' => 25450.00,
            'todayExpenses' => 8200.00,
            'todayProfit' => 17250.00,
            'products' => 125,
            'lowStock' => 5,
            'pendingPurchases' => 2,
            'pendingReimbursements' => 3,
            'attention' => [
                ["5 products are low in stock", 'Review Products & Inventory', route('products'), 'inventory_2'],
                ["2 purchase orders are pending", 'Review Purchase Orders', route('purchases'), 'shopping_cart'],
                ["3 reimbursements need review", 'Review Reimbursements', route('reimbursements'), 'receipt_long'],
            ],
            'activity' => [
                ['Sale completed', 'Rice 25kg · ₱1,250.00', 'payments'],
                ['Expense recorded', 'Store supplies · ₱500.00', 'receipt_long'],
                ['Purchase order created', 'Universal Robina Corp. · ₱28,450.00', 'shopping_cart'],
            ],
        ];

        try {
            DB::connection()->getPdo();

            $dashboard['available'] = true;
            $dashboard['demo'] = false;
            $dashboard['todaySales'] = $this->todayTotal('sales');
            $dashboard['todayExpenses'] = $this->todayTotal('expenses');
            $dashboard['todayProfit'] = $dashboard['todaySales'] !== null && $dashboard['todayExpenses'] !== null
                ? $dashboard['todaySales'] - $dashboard['todayExpenses']
                : null;
            $dashboard['products'] = $this->tableCount('products');
            $dashboard['lowStock'] = $this->lowStockCount();
            $dashboard['pendingPurchases'] = $this->pendingCount('purchase_orders');
            $dashboard['pendingReimbursements'] = $this->pendingCount('reimbursements');
            $dashboard['attention'] = [];
            $dashboard['activity'] = [];

            if ($dashboard['lowStock'] !== null && $dashboard['lowStock'] > 0) {
                $dashboard['attention'][] = [$dashboard['lowStock'].' products are low in stock', 'Review Products & Inventory', route('products'), 'inventory_2'];
            }
            if ($dashboard['pendingPurchases'] !== null && $dashboard['pendingPurchases'] > 0) {
                $dashboard['attention'][] = [$dashboard['pendingPurchases'].' purchase orders are pending', 'Review Purchase Orders', route('purchases'), 'shopping_cart'];
            }
            if ($dashboard['pendingReimbursements'] !== null && $dashboard['pendingReimbursements'] > 0) {
                $dashboard['attention'][] = [$dashboard['pendingReimbursements'].' reimbursements need review', 'Review Reimbursements', route('reimbursements'), 'receipt_long'];
            }
        } catch (\Throwable $exception) {
            report($exception);
        }

        return view('home', compact('dashboard'));
    }

    private function todayTotal(string $table): ?float
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        $amount = collect(['total_amount', 'amount', 'total', 'grand_total'])
            ->first(fn (string $column) => Schema::hasColumn($table, $column));

        if (! $amount) {
            return null;
        }

        $query = DB::table($table);
        if (Schema::hasColumn($table, 'created_at')) {
            $query->whereDate('created_at', today());
        } elseif (Schema::hasColumn($table, 'date')) {
            $query->whereDate('date', today());
        } else {
            return null;
        }

        return (float) $query->sum($amount);
    }

    private function tableCount(string $table): ?int
    {
        return Schema::hasTable($table) ? DB::table($table)->count() : null;
    }

    private function pendingCount(string $table): ?int
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        if (! Schema::hasColumn($table, 'status')) {
            return 0;
        }

        return DB::table($table)->whereIn('status', ['pending', 'draft', 'submitted', 'under_review'])->count();
    }

    private function lowStockCount(): ?int
    {
        if (! Schema::hasTable('products')) {
            return null;
        }

        $stockColumn = collect(['stock', 'quantity', 'current_stock', 'stock_quantity'])
            ->first(fn (string $column) => Schema::hasColumn('products', $column));
        $minimumColumn = collect(['reorder_level', 'minimum_stock', 'min_stock'])
            ->first(fn (string $column) => Schema::hasColumn('products', $column));

        if (! $stockColumn) {
            return null;
        }

        $query = DB::table('products')->where($stockColumn, '<=', 0);
        if ($minimumColumn) {
            $query = DB::table('products')->whereColumn($stockColumn, '<=', $minimumColumn);
        }

        return $query->count();
    }

    public function sales()
    {
        return view('sales');
    }

    public function storeSale(Request $request)
    {
        $validated = $request->validate([
            'customer' => ['required', 'string', 'max:100'],
            'payment_method' => ['required', 'in:cash,gcash,bank,card'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'json'],
        ]);

        $items = json_decode($validated['items'], true);
        if (! is_array($items) || count($items) === 0) {
            return back()->withInput()->withErrors(['sale' => 'Please add at least one product.']);
        }

        $total = collect($items)->sum(fn (array $item) => (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0));
        if ($validated['payment_method'] === 'cash' && (float) $validated['amount_paid'] < $total) {
            return back()->withInput()->withErrors(['sale' => 'Payment is not enough. Please enter the full amount.']);
        }

        return redirect()->route('sales')->with('success', 'Sale completed successfully.');
    }

    public function products()
    {
        return view('products');
    }

    public function reimbursements()
    {
        return view('reimbursements');
    }

    public function purchases()
    {
        return view('purchases');
    }

    public function customers()
    {
        return view('customers');
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:150']]);
        try {
            $customer = Customer::create(['name' => $validated['name'], 'type' => 'business', 'status' => 'active']);
            return response()->json(['id' => $customer->id, 'name' => $customer->name]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => "We couldn't save the customer. Please try again."], 422);
        }
    }

    public function suppliers()
    {
        return view('suppliers');
    }

    public function reports()
    {
        return view('reports');
    }

    public function printReport()
    {
        return view('report-print');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'business_type' => ['required', 'string', 'max:255'],
        ]);

        return 'Generated successfully';
    }
}
