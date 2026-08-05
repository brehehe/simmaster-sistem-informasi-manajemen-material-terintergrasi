<?php

namespace App\Livewire\Actions;

use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;
    public array $notifications = [];
    public bool $showDropdown = false;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        $user = auth()->user();
        if (!$user) return;

        $this->unreadCount = $user->unreadNotifications()->count();
        $this->notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($n) => [
                'id'        => $n->id,
                'read'      => !is_null($n->read_at),
                'data'      => $n->data,
                'time_ago'  => $n->created_at->diffForHumans(),
                'url'       => $n->data['url'] ?? '#',
            ])
            ->toArray();
    }

    public function markAsRead(string $notificationId): void
    {
        $user = auth()->user();
        $notif = $user->notifications()->find($notificationId);
        if ($notif) {
            $notif->markAsRead();
        }
        $this->loadNotifications();
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.actions.notification-bell');
    }
}
