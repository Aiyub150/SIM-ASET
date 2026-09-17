@php
    $startOfMonth = $calDate->copy()->startOfMonth();
    $endOfMonth = $calDate->copy()->endOfMonth();
    $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
    
    $prevMonth = $calDate->copy()->subMonth();
    $nextMonth = $calDate->copy()->addMonth();
    
    $holidayMap = [];
    if(isset($holidays)) {
        foreach($holidays as $h) {
            $holidayMap[$h['date']] = $h;
        }
    }
@endphp

<style>
.cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
}
.cal-day-header {
    text-align: center;
    font-size: 0.75rem;
    font-weight: bold;
    color: #6c757d;
    padding-bottom: 4px;
}
.cal-cell {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    position: relative;
    border: 1px solid #dee2e6;
    background: #fff;
    color: #212529;
}
.cal-cell.empty {
    background: #f8f9fa;
    border-color: #f8f9fa;
}
.cal-cell.sunday {
    color: #dc3545;
}
.cal-cell.today {
    border: 2px solid var(--bs-primary);
}
.cal-cell.holiday {
    background: #dc3545;
    color: #fff;
    border-color: #dc3545;
    cursor: help;
}
.cal-cell.cuti {
    background: #ffc107;
    color: #212529;
    border-color: #ffc107;
    cursor: help;
}
</style>

<div class="calendar-widget position-relative">
    <div id="calendar-loading" class="position-absolute w-100 h-100 d-none" style="background:rgba(255,255,255,0.7); z-index:10; top:0; left:0; display:flex; align-items:center; justify-content:center; border-radius:6px;">
        <div class="spinner-border text-primary spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" onclick="loadCalendar({{ $prevMonth->year }}, {{ $prevMonth->month }})" class="btn btn-sm btn-light border">&laquo;</button>
        <h6 class="mb-0 fw-bold">{{ $calDate->translatedFormat('F Y') }}</h6>
        <button type="button" onclick="loadCalendar({{ $nextMonth->year }}, {{ $nextMonth->month }})" class="btn btn-sm btn-light border">&raquo;</button>
    </div>
    
    <div class="cal-grid mb-2">
        <div class="cal-day-header">Sen</div>
        <div class="cal-day-header">Sel</div>
        <div class="cal-day-header">Rab</div>
        <div class="cal-day-header">Kam</div>
        <div class="cal-day-header">Jum</div>
        <div class="cal-day-header text-primary">Sab</div>
        <div class="cal-day-header text-danger">Min</div>
        
        @for ($i = 1; $i < $startDayOfWeek; $i++)
            <div class="cal-cell empty"></div>
        @endfor
        
        @for ($day = 1; $day <= $endOfMonth->daysInMonth; $day++)
            @php
                $currentDateStr = $calDate->copy()->setDay($day)->format('Y-m-d');
                $isHoliday = isset($holidayMap[$currentDateStr]);
                $holidayData = $isHoliday ? $holidayMap[$currentDateStr] : null;
                
                $isSunday = $calDate->copy()->setDay($day)->dayOfWeekIso === 7;
                $isToday = $currentDateStr === now()->format('Y-m-d');
                
                $classes = ['cal-cell'];
                if ($isToday) $classes[] = 'today';
                
                if ($isHoliday) {
                    if ($holidayData['is_cuti']) {
                        $classes[] = 'cuti';
                    } else {
                        $classes[] = 'holiday';
                    }
                } elseif ($isSunday) {
                    $classes[] = 'sunday';
                }
            @endphp
            <div class="{{ implode(' ', $classes) }}"
                 @if($isHoliday) title="{{ $holidayData['title'] }}" data-bs-toggle="tooltip" @endif>
                {{ $day }}
                @if($isHoliday && $holidayData['is_cuti'])
                    <span style="position: absolute; top:2px; right:2px; font-size:8px;">📌</span>
                @endif
            </div>
        @endfor
    </div>
    
    <div class="mt-3 text-start small">
        <div class="d-flex align-items-center mb-1">
            <div style="width:12px; height:12px;" class="bg-danger rounded me-2"></div>
            <span class="text-muted">Libur Nasional</span>
        </div>
        <div class="d-flex align-items-center">
            <div style="width:12px; height:12px; font-size:8px; display:flex; align-items:center; justify-content:center;" class="bg-warning rounded me-2 text-dark">📌</div>
            <span class="text-muted">Cuti Bersama</span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
