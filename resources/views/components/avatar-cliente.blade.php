@props(['record'])
@if ($record->persona?->fotografia)
    <img src="{{ asset('storage/' . $record->persona->fotografia) }}" style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
@else
    <div style="width:120px;height:120px;border-radius:50%;background:#eee;display:flex;align-items:center;justify-content:center;font-size:24px;color:#888;">Sin foto</div>
@endif
