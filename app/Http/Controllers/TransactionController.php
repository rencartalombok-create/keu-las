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
        $balance = $totalIncome - $totalExpense;

        return Inertia::render('Dashboard', [
            'transactions' => $transactions,
            'summary' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'balance' => $balance
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
        $transaction->delete();
        return redirect()->route('dashboard')->with('success', 'Transaksi berhasil dihapus.');
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

        $transactions = $query->orderBy('date', 'asc')->get();
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $pdf = Pdf::loadView('pdf.report', compact('transactions', 'totalIncome', 'totalExpense', 'balance', 'filter'));
        return $pdf->stream('laporan-keuangan.pdf');
    }
}
