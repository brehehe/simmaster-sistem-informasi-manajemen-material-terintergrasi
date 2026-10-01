<?php

namespace App\Livewire\Admin\Report\Anev;

use App\Exports\ReportingComplianceExport;
use App\Services\ReportingComplianceService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ReportingComplianceIndex extends Component
{
    #[Url]
    public string $date = '';

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $source = 'all';

    #[Url]
    public string $period = 'daily';

    #[Url]
    public string $startDate = '';

    #[Url]
    public string $endDate = '';

    public function updatedPeriod(): void
    {
        $today = CarbonImmutable::now(ReportingComplianceService::TIMEZONE);
        [$this->startDate, $this->endDate] = match ($this->period) {
            'weekly' => [$today->startOfWeek()->toDateString(), $today->toDateString()],
            'monthly' => [$today->startOfMonth()->toDateString(), $today->toDateString()],
            default => [$today->toDateString(), $today->toDateString()],
        };
    }

    private function calendarData(): array
    {
        $today = CarbonImmutable::now(ReportingComplianceService::TIMEZONE)->toDateString();
        $start = $this->period === 'daily' ? $this->date : $this->startDate;
        $end = $this->period === 'daily' ? $this->date : $this->endDate;
        $validation = validator(compact('start', 'end'), ['start' => 'required|date_format:Y-m-d|before_or_equal:end', 'end' => 'required|date_format:Y-m-d|before_or_equal:'.$today]);
        if ($validation->fails()) {
            return ['days' => [], 'rows' => collect(), 'error' => 'Pilih rentang tanggal yang valid, maksimal hari ini.'];
        }
        try {
            $calendar = app(ReportingComplianceService::class)->calendar($start, $end);
            $calendar['rows'] = $calendar['rows']->filter(fn ($row) => $this->search === '' || mb_stripos($row['name'], trim($this->search)) !== false)->values();

            return $calendar + ['error' => null];
        } catch (\InvalidArgumentException $e) {
            return ['days' => [], 'rows' => collect(), 'error' => $e->getMessage()];
        }
    }

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('Admin'), 403);
    }

    public function exportExcel()
    {
        abort_unless(auth()->user()?->hasRole('Admin'), 403);
        $today = CarbonImmutable::now(ReportingComplianceService::TIMEZONE)->toDateString();
        $this->validate([
            'date' => 'required|date_format:Y-m-d|before_or_equal:'.$today,
            'source' => ['required', Rule::in(array_merge(['all'], array_keys(ReportingComplianceService::sources())))],
            'status' => ['nullable', Rule::in(['', 'missing', 'green', 'yellow', 'red'])],
        ]);
        $rows = $this->filteredRows(app(ReportingComplianceService::class)->rows($this->date, $this->source));

        return Excel::download(new ReportingComplianceExport($rows, $this->date, $this->source), 'anev-ketertiban-laporan-'.$this->date.'.xlsx');
    }

    private function filteredRows(Collection $rows): Collection
    {
        return $rows->filter(function ($row) {
            $matchesStatus = $this->status === '' || ($this->status === 'missing' ? ! $row['reported'] : $row['color'] === $this->status);

            return $matchesStatus && ($this->search === '' || mb_stripos($row['name'], trim($this->search)) !== false);
        })->values();
    }

    public function mount(): void
    {
        if ($this->startDate === '' || $this->endDate === '') {
            $this->updatedPeriod();
        }
        if ($this->date === '') {
            $this->date = CarbonImmutable::now(ReportingComplianceService::TIMEZONE)->toDateString();
        }
    }

    public function render()
    {
        $today = CarbonImmutable::now(ReportingComplianceService::TIMEZONE)->toDateString();
        $validator = validator(['date' => $this->date], ['date' => 'required|date_format:Y-m-d|before_or_equal:'.$today]);
        $dateError = $validator->fails() ? 'Pilih tanggal laporan yang valid, maksimal hari ini.' : null;
        $sources = ReportingComplianceService::sources();
        $sourceError = $this->source !== 'all' && ! isset($sources[$this->source]) ? 'Jenis input tidak valid.' : null;
        $all = ($dateError || $sourceError) ? collect() : app(ReportingComplianceService::class)->rows($this->date, $this->source);
        $summary = [
            'total' => $all->count(),
            'green' => $all->where('color', 'green')->count(),
            'yellow' => $all->where('color', 'yellow')->count(),
            'red' => $all->where('color', 'red')->count(),
            'missing' => $all->where('reported', false)->count(),
        ];
        $rows = $this->filteredRows($all);

        $calendar = $this->calendarData();

        return view('livewire.admin.report.anev.reporting-compliance-index', compact('calendar', 'rows', 'summary', 'today', 'dateError', 'sourceError', 'sources'))
            ->layout('components.layouts.main.app', ['title' => 'Anev Ketertiban Laporan']);
    }
}
