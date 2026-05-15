<x-filament-widgets::widget class="fi-admin-quick-actions">
    <div class="admin-quick-shell">
        <div class="admin-quick-copy">
            <p class="admin-quick-eyebrow">Quick Access</p>
            <h2 class="admin-quick-heading">Buka area kerja admin tanpa banyak langkah.</h2>
        </div>

        <div class="admin-quick-grid">
            @foreach ($items as $item)
                <a href="{{ $item['url'] }}" class="admin-quick-card admin-quick-card-{{ $item['theme'] }}">
                    <span class="admin-quick-icon" aria-hidden="true">
                        @switch($item['icon'])
                            @case('link')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.12 14.88a3 3 0 0 1 0-4.243l3.536-3.536a3 3 0 1 1 4.243 4.243l-1.768 1.768" />
                                    <path d="m8.868 13.364-1.768 1.768a3 3 0 1 1-4.243-4.243l3.536-3.536a3 3 0 0 1 4.243 0" />
                                    <path d="m8.5 15.5 7-7" />
                                </svg>
                                @break
                            @case('shield')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3.75 6.75 6v4.592c0 3.506 2.22 6.744 5.25 7.908 3.03-1.164 5.25-4.402 5.25-7.908V6L12 3.75Z" />
                                    <path d="M12 8.5v4.75" />
                                    <path d="M9.625 10.875h4.75" />
                                </svg>
                                @break
                            @case('grid')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="4" width="7" height="7" rx="2" />
                                    <rect x="13" y="4" width="7" height="7" rx="2" />
                                    <rect x="4" y="13" width="7" height="7" rx="2" />
                                    <rect x="13" y="13" width="7" height="7" rx="2" />
                                </svg>
                                @break
                            @case('users')
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15.5 8.25a3.25 3.25 0 1 1-6.5 0 3.25 3.25 0 0 1 6.5 0Z" />
                                    <path d="M4.75 18.25a7.25 7.25 0 0 1 14.5 0" />
                                </svg>
                                @break
                        @endswitch
                    </span>

                    <span class="admin-quick-text">
                        <span class="admin-quick-label">{{ $item['label'] }}</span>
                        <span class="admin-quick-hint">{{ $item['hint'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
