<?php

namespace App\Exports;

use App\Models\Reception\Reception;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BappmExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $reception;

    public function __construct(Reception $reception)
    {
        $this->reception = $reception;
    }

    public function view(): View
    {
        return view('exports.bappm_excel', [
            'reception' => $this->reception,
            'receptionDetails' => $this->reception->getGroupedItems()
        ]);
    }

    public function title(): string
    {
        // Short title suitable for sheet tab name
        return 'BAPPM - ' . substr(preg_replace('/[^A-Za-z0-9]/', '', $this->reception->code), -10);
    }
}
