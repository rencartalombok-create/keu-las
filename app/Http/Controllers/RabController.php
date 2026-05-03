<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use App\Models\RabItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class RabController extends Controller
{
    public function index()
    {
        $rabs = Rab::with('items')->orderBy('date', 'desc')->get();
        return Inertia::render('Rab/Index', ['rabs' => $rabs]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $totalAmount = 0;
        foreach ($validated['items'] as &$item) {
            $item['total_price'] = $item['quantity'] * $item['unit_price'];
            $totalAmount += $item['total_price'];
        }

        $rab = Rab::create([
            'project_name' => $validated['project_name'],
            'customer_name' => $validated['customer_name'],
            'date' => $validated['date'],
            'total_amount' => $totalAmount,
        ]);

        foreach ($validated['items'] as $item) {
            $rab->items()->create($item);
        }

        return redirect()->back()->with('success', 'RAB berhasil dibuat.');
    }

    public function destroy(Rab $rab)
    {
        $rab->delete();
        return redirect()->back()->with('success', 'RAB berhasil dihapus.');
    }

    public function report(Rab $rab)
    {
        $rab->load('items');
        $pdf = Pdf::loadView('pdf.rab', compact('rab'));
        return $pdf->stream('RAB-' . $rab->project_name . '.pdf');
    }
}
