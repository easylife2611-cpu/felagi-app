{{-- ============================================================
     FELAGI BOTTOM NAVIGATION — L349 Design Polish
     - Single icon family (Heroicons outline, 24×24)
     - Equal spacing (flex-1)
     - Active state: color + weight + top indicator
     - Create as separate FAB (Primary action)
     - Notifications moved to top bar (top-bar__alerts)
     ============================================================ --}}
@php
    $currentPath = trim(request()->path(), '/');
    $navItems = [
        ['href' => '/browse',    'label' => 'አስስ',     'icon' => 'search',    'match' => 'browse'],
        ['href' => '/my/needs',  'label' => 'ፍላጎቶቼ',  'icon' => 'clipboard', 'match' => 'my/needs'],
        ['href' => '/my/offers', 'label' => 'ቅናሾቼ',   'icon' => 'tag',       'match' => 'my/offers'],
        ['href' => '/profile',   'label' => 'መገለጫ',    'icon' => 'user',      'match' => 'profile'],
    ];
@endphp

{{-- FAB — Primary action: Create Need (visually separated) --}}
<a href="/needs/new"
   class="fab felagi-fab"
   title="ፍጠር"
   aria-label="አዲስ ፍላጎት ፍጠር">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 4.5v15m7.5-7.5h-15"/>
    </svg>
</a>

{{-- Bottom Navigation — 4 primary destinations --}}
<nav class="nav felagi-nav" role="navigation" aria-label="ዋና ዳሰሳ">
    @foreach ($navItems as $item)
        @php
            $isActive = $currentPath === $item['match']
                     || str_starts_with($currentPath, $item['match'] . '/');
        @endphp
        <a href="{{ $item['href'] }}"
           class="felagi-nav__item {{ $isActive ? 'is-active' : '' }}"
           @if($isActive) aria-current="page" @endif>

            <span class="felagi-nav__indicator" aria-hidden="true"></span>

            <span class="felagi-nav__icon" aria-hidden="true">
                @switch($item['icon'])
                    @case('search')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-3.5-3.5"/>
                        </svg>
                        @break

                    @case('clipboard')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="3" width="14" height="18" rx="2"/>
                            <path d="M9 8h6M9 12h6M9 16h4"/>
                        </svg>
                        @break

                    @case('tag')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L3 13V5a2 2 0 0 1 2-2h8l7.59 7.59a2 2 0 0 1 0 2.82Z"/>
                            <circle cx="7.5" cy="7.5" r="1"/>
                        </svg>
                        @break

                    @case('user')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>
                        @break
                @endswitch
            </span>

            <span class="felagi-nav__label">{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
