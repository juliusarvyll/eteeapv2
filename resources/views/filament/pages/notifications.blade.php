<x-filament::page>
    <div class="space-y-6">
        <div class="flex items-center justify-between pb-4 border-b fi-section-border">
            <h1 class="text-2xl font-bold fi-section-heading">
                Notifications
            </h1>
            <x-filament::button
                icon="heroicon-o-check-circle"
                wire:click="markAllAsRead"
                color="primary"
            >
                Mark All as Read
            </x-filament::button>
        </div>

        <div class="space-y-4">
            @forelse(auth()->user()->notifications as $notification)
                <div class="relative flex gap-4 p-4 fi-card rounded-lg shadow-sm ring-1 fi-ring transition-all duration-200 {{ $notification->read_at ? 'opacity-75' : '' }}">
                    <div class="flex-shrink-0 pt-1">
                        @if(isset($notification->data['status']) && $notification->data['status'] === 'started')
                            <div class="p-2 rounded-full bg-blue-50 dark:bg-blue-900/20">
                                <x-heroicon-o-document-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                            </div>
                        @else
                            <div class="p-2 rounded-full bg-emerald-50 dark:bg-emerald-900/20">
                                <x-heroicon-o-document-check class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-base font-semibold fi-card-header">
                                {{ $notification->data['title'] }}
                            </h3>
                            @if(isset($notification->data['status']))
                                <span @class([
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' => $notification->data['status'] === 'started',
                                    'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' => $notification->data['status'] === 'submitted'
                                ])>
                                    {{ strtoupper($notification->data['status']) }}
                                </span>
                            @endif
                        </div>

                        <p class="text-sm fi-card-description">
                            {{ $notification->data['message'] }}
                        </p>

                        <div class="flex items-center justify-between mt-3">
                            <time class="text-xs fi-card-meta">
                                {{ $notification->created_at->diffForHumans() }}
                            </time>
                            <div class="flex gap-2">
                                <button
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    class="text-xs fi-card-meta hover:text-primary-600 dark:hover:text-primary-400"
                                >
                                    Mark as read
                                </button>
                                <button
                                    wire:click="deleteNotification('{{ $notification->id }}')"
                                    class="text-xs fi-card-meta hover:text-danger-600 dark:hover:text-danger-400"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center fi-card rounded-lg">
                    <x-heroicon-o-bell-alert class="w-8 h-8 mx-auto text-gray-400 dark:text-gray-500" />
                    <h3 class="mt-4 text-sm font-medium fi-card-header">
                        No notifications
                    </h3>
                    <p class="mt-1 text-sm fi-card-description">
                        You're all caught up!
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament::page>
