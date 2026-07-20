<?php

namespace App\Livewire\Admin\Regulation;

use App\Models\Regulation\Regulation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AdminRegulationIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public int $perPage = 10;

    #[Url]
    public string $filterCategory = '';

    #[Url]
    public string $filterYear = '';

    // Modals
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public ?string $selectedId = null;

    // Form fields
    public string $title = '';
    public string $category = 'perpol';
    public string $number = '';
    public ?int $year = null;
    public string $description = '';
    public $file = null;
    public ?string $existingFilePath = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterYear' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        $this->year = (int) now()->year;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterCategory()
    {
        $this->resetPage();
    }

    public function updatedFilterYear()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->selectedId = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->title = '';
        $this->category = 'perpol';
        $this->number = '';
        $this->year = (int) now()->year;
        $this->description = '';
        $this->file = null;
        $this->existingFilePath = null;
        $this->resetErrorBag();
    }

    public function saveRegulation()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:perpol,perkap,st,jukrah,sop,lainnya',
            'number' => 'required|string|max:255',
            'year' => 'required|integer|min:1945|max:2100',
            'description' => 'nullable|string',
            'file' => $this->selectedId ? 'nullable|file|mimes:pdf,doc,docx|max:20480' : 'required|file|mimes:pdf,doc,docx|max:20480',
        ], [
            'title.required' => 'Judul peraturan wajib diisi',
            'number.required' => 'Nomor peraturan wajib diisi',
            'file.required' => 'File dokumen PDF wajib diunggah',
            'file.mimes' => 'Format file harus PDF atau Word document (DOC/DOCX)',
            'file.max' => 'Ukuran file maksimal 20MB',
        ]);

        $filePath = $this->existingFilePath;
        $fileName = null;
        $fileSize = null;

        if ($this->file) {
            $originalName = $this->file->getClientOriginalName();
            $fileSize = $this->file->getSize();
            $storedPath = $this->file->storeAs('regulations', time() . '_' . $originalName, 'public');
            $filePath = $storedPath;
            $fileName = $originalName;
        }

        if ($this->selectedId) {
            $regulation = Regulation::findOrFail($this->selectedId);
            $updateData = [
                'title' => $this->title,
                'category' => $this->category,
                'number' => $this->number,
                'year' => $this->year,
                'description' => $this->description,
            ];
            if ($filePath) {
                $updateData['file_path'] = $filePath;
                if ($fileName) $updateData['file_name'] = $fileName;
                if ($fileSize) $updateData['file_size'] = $fileSize;
            }
            $regulation->update($updateData);
            session()->flash('success', 'Dokumen peraturan berhasil diperbarui.');
        } else {
            Regulation::create([
                'code' => Regulation::generateCode(),
                'title' => $this->title,
                'category' => $this->category,
                'number' => $this->number,
                'year' => $this->year,
                'description' => $this->description,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'created_by' => Auth::id(),
                'is_active' => true,
            ]);
            session()->flash('success', 'Dokumen peraturan baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function editRegulation($id)
    {
        $regulation = Regulation::findOrFail($id);
        $this->selectedId = $regulation->id;
        $this->title = $regulation->title;
        $this->category = $regulation->category;
        $this->number = $regulation->number;
        $this->year = $regulation->year;
        $this->description = $regulation->description ?? '';
        $this->existingFilePath = $regulation->file_path;
        $this->showEditModal = true;
    }

    public function openDeleteModal($id)
    {
        $this->selectedId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRegulation()
    {
        if ($this->selectedId) {
            $regulation = Regulation::find($this->selectedId);
            if ($regulation) {
                $regulation->delete();
                session()->flash('success', 'Dokumen peraturan berhasil dihapus.');
            }
        }
        $this->closeModal();
    }

    public function render()
    {
        // Category counts
        $totalCount = Regulation::where('is_active', true)->count();
        $perpolCount = Regulation::where('is_active', true)->where('category', 'perpol')->count();
        $perkapCount = Regulation::where('is_active', true)->where('category', 'perkap')->count();
        $stCount = Regulation::where('is_active', true)->where('category', 'st')->count();
        $jukrahCount = Regulation::where('is_active', true)->whereIn('category', ['jukrah', 'sop'])->count();

        $query = Regulation::with('creator')->where('is_active', true);

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterYear) {
            $query->where('year', $this->filterYear);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'ilike', '%' . $this->search . '%')
                  ->orWhere('number', 'ilike', '%' . $this->search . '%')
                  ->orWhere('description', 'ilike', '%' . $this->search . '%')
                  ->orWhere('code', 'ilike', '%' . $this->search . '%');
            });
        }

        $regulations = $query->orderBy('year', 'desc')->latest()->paginate($this->perPage);

        $availableYears = Regulation::where('is_active', true)
            ->distinct()
            ->pluck('year')
            ->sortDesc()
            ->toArray();

        return view('livewire.admin.regulation.admin-regulation-index', [
            'regulations' => $regulations,
            'totalCount' => $totalCount,
            'perpolCount' => $perpolCount,
            'perkapCount' => $perkapCount,
            'stCount' => $stCount,
            'jukrahCount' => $jukrahCount,
            'availableYears' => $availableYears,
        ])->layout('components.layouts.main.app');
    }
}
