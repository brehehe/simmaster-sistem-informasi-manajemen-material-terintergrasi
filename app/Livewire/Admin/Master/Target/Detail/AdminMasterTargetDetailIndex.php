<?php

namespace App\Livewire\Admin\Master\Target\Detail;

use App\Models\Police\RegionalPolice;
use App\Models\Target\Target;
use App\Models\Target\TargetDetail;
use App\Models\Type\Type;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminMasterTargetDetailIndex extends Component
{
    public ?string $targetId = null;

    public string $name = '';

    public int $year;

    public ?string $description = null;

    public bool $is_active = true;

    public array $types = [];

    public array $rows = [];

    public array $matrix = [];

    public function mount(?string $target_id = null): void
    {
        $this->targetId = $target_id;
        $this->year = (int) now()->format('Y');

        $this->types = Type::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Type $type) => ['id' => $type->id, 'name' => $type->name])
            ->values()
            ->toArray();

        $this->rows = $this->buildRows();
        $this->matrix = $this->buildDefaultMatrix();

        if ($this->targetId) {
            $target = Target::findOrFail($this->targetId);
            $this->name = $target->name;
            $this->year = (int) $target->year;
            $this->description = $target->description;
            $this->is_active = (bool) $target->is_active;

            $details = TargetDetail::query()
                ->where('target_id', $target->id)
                ->get();

            foreach ($details as $detail) {
                $rowKey = $this->rowKeyFromDetail($detail);
                if ($rowKey && isset($this->matrix[$rowKey][$detail->type_id])) {
                    $this->matrix[$rowKey][$detail->type_id] = (float) $detail->quantity;
                }
            }
        } else {
            $this->name = "Target Ditlantas {$this->year}";
        }
    }

    public function updatedYear(): void
    {
        if (! $this->targetId) {
            $this->name = "Target Ditlantas {$this->year}";
        }
    }

    protected function buildRows(): array
    {
        $rows = [];
        $regionalPolice = RegionalPolice::query()
            ->with(['policeStations' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        foreach ($regionalPolice as $regional) {
            $rows[] = [
                'key' => $this->rowKey('regional', $regional->id),
                'label' => $regional->name,
                'regional_police_id' => $regional->id,
                'police_station_id' => null,
                'level' => 'regional',
            ];

            foreach ($regional->policeStations as $station) {
                $rows[] = [
                    'key' => $this->rowKey('station', $station->id),
                    'label' => $station->name,
                    'regional_police_id' => $station->regional_police_id,
                    'police_station_id' => $station->id,
                    'level' => 'station',
                ];
            }
        }

        return $rows;
    }

    protected function buildDefaultMatrix(): array
    {
        $matrix = [];

        foreach ($this->rows as $row) {
            foreach ($this->types as $type) {
                $matrix[$row['key']][$type['id']] = 0;
            }
        }

        return $matrix;
    }

    protected function rowKey(string $prefix, string $id): string
    {
        return $prefix.'_'.$id;
    }

    protected function rowKeyFromDetail(TargetDetail $detail): ?string
    {
        if ($detail->police_station_id) {
            return $this->rowKey('station', $detail->police_station_id);
        }

        if ($detail->regional_police_id) {
            return $this->rowKey('regional', $detail->regional_police_id);
        }

        return null;
    }

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        foreach ($this->matrix as $rowKey => $rowValues) {
            foreach ($rowValues as $typeId => $value) {
                $rules["matrix.$rowKey.$typeId"] = 'nullable|numeric|min:0';
            }
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama target wajib diisi.',
            'name.max' => 'Nama target maksimal 255 karakter.',
            'year.required' => 'Tahun target wajib diisi.',
            'year.integer' => 'Tahun target harus berupa angka.',
        ];
    }

    public function rowTotal(string $rowKey): float
    {
        $row = $this->matrix[$rowKey] ?? [];

        return array_sum(array_map(static fn ($value) => (float) $value, $row));
    }

    public function columnTotal(string $typeId): float
    {
        $total = 0;

        foreach ($this->matrix as $row) {
            $total += (float) ($row[$typeId] ?? 0);
        }

        return $total;
    }

    public function grandTotal(): float
    {
        $total = 0;

        foreach (array_keys($this->matrix) as $rowKey) {
            $total += $this->rowTotal($rowKey);
        }

        return $total;
    }

    public function save(): void
    {
        if (! $this->targetId) {
            $this->name = "Target Ditlantas {$this->year}";
        }

        $this->validate();

        DB::transaction(function () {
            $target = $this->targetId
                ? Target::findOrFail($this->targetId)
                : new Target;

            $target->fill([
                'name' => $this->name,
                'year' => $this->year,
                'description' => $this->description,
                'is_active' => $this->is_active,
            ]);
            $target->save();

            $this->targetId = $target->id;

            $activeKeys = [];

            foreach ($this->rows as $row) {
                foreach ($this->types as $type) {
                    $value = (float) ($this->matrix[$row['key']][$type['id']] ?? 0);

                    if ($value <= 0) {
                        continue;
                    }

                    $attributes = [
                        'target_id' => $target->id,
                        'regional_police_id' => $row['regional_police_id'],
                        'police_station_id' => $row['police_station_id'],
                        'type_id' => $type['id'],
                        'type_detail_id' => null,
                    ];

                    TargetDetail::updateOrCreate($attributes, [
                        'name' => $row['label'],
                        'quantity' => $value,
                        'description' => $this->description,
                        'is_active' => $this->is_active,
                    ]);

                    $activeKeys[] = $this->detailKey($attributes);
                }
            }

            $existing = TargetDetail::query()->where('target_id', $target->id)->get();

            foreach ($existing as $detail) {
                $key = $this->detailKey([
                    'target_id' => $detail->target_id,
                    'regional_police_id' => $detail->regional_police_id,
                    'police_station_id' => $detail->police_station_id,
                    'type_id' => $detail->type_id,
                    'type_detail_id' => $detail->type_detail_id,
                ]);

                if (! in_array($key, $activeKeys, true)) {
                    $detail->delete();
                }
            }
        });

        session()->flash('success', 'Target berhasil disimpan.');

        if ($this->targetId) {
            $this->redirect(route('master.target.detail', ['target_id' => $this->targetId]), navigate: true);
        }
    }

    protected function detailKey(array $attributes): string
    {
        return implode('|', [
            $attributes['target_id'] ?? '-',
            $attributes['regional_police_id'] ?? '-',
            $attributes['police_station_id'] ?? '-',
            $attributes['type_id'] ?? '-',
            $attributes['type_detail_id'] ?? '-',
        ]);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.master.target.detail.admin-master-target-detail-index')
            ->layout('components.layouts.main.app');
    }
}
