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

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $rab = Rab::create([
                'project_name' => $validated['project_name'],
                'customer_name' => $validated['customer_name'],
                'date' => $validated['date'],
                'total_amount' => $totalAmount,
            ]);

            foreach ($validated['items'] as $item) {
                $rab->items()->create($item);
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'RAB berhasil dibuat.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat RAB: ' . $e->getMessage());
        }
    }

    public function destroy(Rab $rab)
    {
        try {
            $id = $rab->id;
            \Illuminate\Support\Facades\Log::info("Attempting to delete RAB ID: {$id}");

            $rab->delete();

            \Illuminate\Support\Facades\Log::info("Successfully deleted RAB ID: {$id}");
            return redirect()->back()->with('success', 'RAB berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to delete RAB: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus RAB: ' . $e->getMessage());
        }
    }

    public function report(Rab $rab)
    {
        $rab->load('items');
        $pdf = Pdf::loadView('pdf.rab', compact('rab'));
        return $pdf->stream('RAB-' . $rab->project_name . '.pdf');
    }
}
