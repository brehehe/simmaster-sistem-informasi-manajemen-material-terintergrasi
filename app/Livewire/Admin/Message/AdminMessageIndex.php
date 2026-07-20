<?php

namespace App\Livewire\Admin\Message;

use App\Models\Message\Message;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AdminMessageIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public int $perPage = 10;
    public string $activeTab = 'inbox'; // 'inbox', 'sent'

    #[Url]
    public string $filterCategory = '';

    #[Url]
    public string $filterReadStatus = '';

    // Modals
    public bool $showCreateModal = false;
    public bool $showDetailModal = false;
    public ?string $selectedId = null;

    // Form fields
    public string $subject = '';
    public string $category = 'general_info';
    public string $receiver_type = 'all'; // 'all', 'polda', 'polres'
    public ?string $receiver_regional_police_id = null;
    public ?string $receiver_police_station_id = null;
    public string $message_text = '';
    public $attachment = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterReadStatus' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterCategory()
    {
        $this->resetPage();
    }

    public function updatedFilterReadStatus()
    {
        $this->resetPage();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
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
        $this->showDetailModal = false;
        $this->selectedId = null;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->subject = '';
        $this->category = 'general_info';
        $this->receiver_type = 'all';
        $this->receiver_regional_police_id = null;
        $this->receiver_police_station_id = null;
        $this->message_text = '';
        $this->attachment = null;
        $this->resetErrorBag();
    }

    public function sendMessage()
    {
        $user = Auth::user();

        $this->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:material_damage,cross_subsidy,general_info',
            'message_text' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
        ], [
            'subject.required' => 'Subjek pesan wajib diisi',
            'message_text.required' => 'Isi pesan tidak boleh kosong',
            'attachment.max' => 'Ukuran file maksimal 10MB',
        ]);

        $attachmentPath = null;
        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('messages', 'public');
        }

        Message::create([
            'code' => Message::generateCode(),
            'sender_id' => $user->id,
            'sender_regional_police_id' => $user->regional_police_id,
            'sender_police_station_id' => $user->police_station_id,
            'receiver_type' => $this->receiver_type,
            'receiver_regional_police_id' => $this->receiver_regional_police_id ?: null,
            'receiver_police_station_id' => $this->receiver_police_station_id ?: null,
            'category' => $this->category,
            'subject' => $this->subject,
            'message' => $this->message_text,
            'attachment_path' => $attachmentPath,
            'is_read' => false,
            'is_active' => true,
        ]);

        session()->flash('success', 'Pesan berhasil dikirim.');
        $this->closeModal();
    }

    public function viewMessage($id)
    {
        $this->selectedId = $id;
        $message = Message::find($id);
        
        if ($message) {
            $user = Auth::user();
            // Mark as read if user is the receiver or in inbox tab
            if ($message->sender_id !== $user->id && !$message->is_read) {
                $message->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            }
            $this->showDetailModal = true;
        }
    }

    public function replyMessage($id)
    {
        $msg = Message::find($id);
        if ($msg) {
            $this->showDetailModal = false;
            $this->resetForm();
            $this->subject = 'RE: ' . $msg->subject;
            $this->category = $msg->category;
            
            if ($msg->sender_police_station_id) {
                $this->receiver_type = 'polres';
                $this->receiver_police_station_id = $msg->sender_police_station_id;
            } elseif ($msg->sender_regional_police_id) {
                $this->receiver_type = 'polda';
                $this->receiver_regional_police_id = $msg->sender_regional_police_id;
            }
            
            $this->showCreateModal = true;
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Statistics
        $totalMessagesCount = Message::where('is_active', true)
            ->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_regional_police_id', $user->regional_police_id)
                  ->orWhere('receiver_police_station_id', $user->police_station_id)
                  ->orWhere('receiver_type', 'all');
            })->count();

        $unreadCount = Message::where('is_active', true)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->where(function($q) use ($user) {
                $q->where('receiver_regional_police_id', $user->regional_police_id)
                  ->orWhere('receiver_police_station_id', $user->police_station_id)
                  ->orWhere('receiver_type', 'all');
            })->count();

        $damageNotifCount = Message::where('is_active', true)
            ->where('category', 'material_damage')
            ->count();

        $subsidyNotifCount = Message::where('is_active', true)
            ->where('category', 'cross_subsidy')
            ->count();

        // Query messages
        $query = Message::with(['sender', 'senderRegionalPolice', 'senderPoliceStation', 'receiverRegionalPolice', 'receiverPoliceStation'])
            ->where('is_active', true);

        if ($this->activeTab === 'inbox') {
            $query->where('sender_id', '!=', $user->id);
            $query->where(function($q) use ($user) {
                if ($user->hasRole('Admin')) {
                    // Admin sees all inbox
                } elseif ($user->hasRole('Polda')) {
                    $q->where('receiver_regional_police_id', $user->regional_police_id)
                      ->orWhere('receiver_type', 'polda')
                      ->orWhere('receiver_type', 'all');
                } elseif ($user->hasRole('Polres')) {
                    $q->where('receiver_police_station_id', $user->police_station_id)
                      ->orWhere('receiver_type', 'polres')
                      ->orWhere('receiver_type', 'all');
                }
            });
        } else {
            // Sent items
            $query->where('sender_id', $user->id);
        }

        // Filters
        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterReadStatus === 'unread') {
            $query->where('is_read', false);
        } elseif ($this->filterReadStatus === 'read') {
            $query->where('is_read', true);
        }

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('subject', 'ilike', '%' . $this->search . '%')
                  ->orWhere('message', 'ilike', '%' . $this->search . '%')
                  ->orWhere('code', 'ilike', '%' . $this->search . '%');
            });
        }

        $messages = $query->latest()->paginate($this->perPage);

        $selectedMessage = null;
        if ($this->selectedId) {
            $selectedMessage = Message::with(['sender', 'senderRegionalPolice', 'senderPoliceStation', 'receiverRegionalPolice', 'receiverPoliceStation'])->find($this->selectedId);
        }

        $regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();
        $policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.message.admin-message-index', [
            'messages' => $messages,
            'totalMessagesCount' => $totalMessagesCount,
            'unreadCount' => $unreadCount,
            'damageNotifCount' => $damageNotifCount,
            'subsidyNotifCount' => $subsidyNotifCount,
            'selectedMessage' => $selectedMessage,
            'regionalPolices' => $regionalPolices,
            'policeStations' => $policeStations,
        ])->layout('components.layouts.main.app');
    }
}
