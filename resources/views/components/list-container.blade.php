@props([
    'id' => '',
    'position' => 'primary', // primary, secondary
    'theme' => 'red', // red, blue, green, orange, purple
    'title' => '',
    'icon' => 'list',
    'count' => 0,
    'items' => [],
    'itemType' => 'default' // default, cctv, virtual-tour
])

{{-- 
Universal List Container Component
Supports both object and array data formats
--}}

<div id="{{ $id }}" class="list-container {{ $position }} theme-{{ $theme }}">
    <h4 class="list-header">
        <i data-lucide="{{ $icon }}" style="width: 16px; height: 16px;"></i>
        {{ $count }} {{ $title }}
    </h4>
    <div class="list-content">
        {{-- CCTV Item Type: Expects objects with id_cctv, nama_lokasi, link, lat, lang, count_seen --}}
        @if($itemType === 'cctv')
            @foreach($items as $index => $item)
                <div class="list-item" onclick="focusOnMarker({{ $index }}, {{ $item->id_cctv }}, '{{ $item->nama_lokasi ?? 'CCTV' }}', '{{ $item->link }}', {{ $item->lat }}, {{ $item->lang }}, {{ $item->count_seen ?? 0 }})">
                    <div class="list-item-left">
                        <i data-lucide="cctv" class="list-item-icon"></i>
                        <span class="list-item-name">{{ $item->nama_lokasi ?? 'CCTV ' . ($index + 1) }}</span>
                    </div>
                    <div class="list-item-right">
                        <span class="list-item-status">Online</span>
                        <span class="list-item-meta" id="view-count-{{ $item->id_cctv }}">{{ number_format($item->count_seen ?? 0) }} views</span>
                    </div>
                </div>
            @endforeach
        {{-- Virtual Tour Item Type: Expects objects with nama_lokasi, link, lat, lang --}}
        @elseif($itemType === 'virtual-tour')
            @foreach($items as $index => $item)
                <div class="list-item" onclick="focusOnVirtualTour({{ $index }}, '{{ $item->nama_lokasi }}', '{{ $item->link }}', {{ $item->lat }}, {{ $item->lang }})">
                    <div class="list-item-left">
                        <i data-lucide="binoculars" class="list-item-icon"></i>
                        <span class="list-item-name">{{ $item->nama_lokasi }}</span>
                    </div>
                    <div class="list-item-right">
                        <span class="list-item-status">Tersedia</span>
                        <span class="list-item-meta">360° Tour</span>
                    </div>
                </div>
            @endforeach
        {{-- Default Item Type: Supports both objects and arrays with flexible properties --}}
        @else
            @foreach($items as $index => $item)
                <div class="list-item">
                    <div class="list-item-left">
                        <i data-lucide="{{ is_object($item) ? ($item->icon ?? 'map-pin') : ($item['icon'] ?? 'map-pin') }}" class="list-item-icon"></i>
                        <span class="list-item-name">
                            @if(is_object($item))
                                {{ $item->name ?? $item->title ?? $item->nama_lokasi ?? 'Item ' . ($index + 1) }}
                            @else
                                {{ $item['name'] ?? $item['title'] ?? 'Item ' . ($index + 1) }}
                            @endif
                        </span>
                    </div>
                    <div class="list-item-right">
                        <span class="list-item-status">
                            @if(is_object($item))
                                {{ $item->status ?? 'Active' }}
                            @else
                                {{ $item['status'] ?? 'Active' }}
                            @endif
                        </span>
                        @if(is_object($item) && isset($item->meta))
                            <span class="list-item-meta">{{ $item->meta }}</span>
                        @elseif(is_array($item) && isset($item['meta']))
                            <span class="list-item-meta">{{ $item['meta'] }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
