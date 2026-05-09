<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $query = Transaction::query();

        if ($filter === 'this_month') {
            $query->whereMonth('date', \Carbon\Carbon::now()->month)
                  ->whereYear('date', \Carbon\Carbon::now()->year);
        } elseif ($filter === 'last_month') {
            $lastMonth = \Carbon\Carbon::now()->subMonth();
            $query->whereMonth('date', $lastMonth->month)
                  ->whereYear('date', $lastMonth->year);
        }

        $transactions = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        
        // Cumulative balance should be calculated from all transactions for accuracy
        $actualBalance = Transaction::where('type', 'income')->sum('amount') - Transaction::where('type', 'expense')->sum('amount');

        return Inertia::render('Dashboard', [
            'transactions' => $transactions,
            'summary' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'balance' => $actualBalance
            ],
            'currentFilter' => $filter
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
        ]);

        Transaction::create($validated);

        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
        ]);

        $transaction->update($validated);

        return redirect()->back()->with('success', 'Transaksi berhasil diubah.');
    }

    public function destroy(Transaction $transaction)
    {
        try {
            $id = $transaction->id;
            \Illuminate\Support\Facades\Log::info("Attempting to delete transaction ID: {$id}");
            
            $transaction->delete();
            
            \Illuminate\Support\Facades\Log::info("Successfully deleted transaction ID: {$id}");
            return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to delete transaction: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    public function report(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $query = Transaction::query();

        if ($filter === 'this_month') {
            $query->whereMonth('date', \Carbon\Carbon::now()->month)
                  ->whereYear('date', \Carbon\Carbon::now()->year);
        } elseif ($filter === 'last_month') {
            $lastMonth = \Carbon\Carbon::now()->subMonth();
            $query->whereMonth('date', $lastMonth->month)
                  ->whereYear('date', $lastMonth->year);
        }

        $transactions = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $pdf = Pdf::loadView('pdf.report', compact('transactions', 'totalIncome', 'totalExpense', 'balance', 'filter'));
        
        if ($request->has('download')) {
            return $pdf->download('laporan-keuangan-' . now()->format('Y-m-d') . '.pdf');
        }

        return $pdf->stream('laporan-keuangan.pdf');
    }
}
