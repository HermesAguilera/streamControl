@php
    use Picqer\Barcode\BarcodeGeneratorSVG;
    $generator = new BarcodeGeneratorSVG();
@endphp

@if ($getState())
    <div style="text-align:center;">
        {!! $generator->getBarcode($getState(), $generator::TYPE_CODE_128) !!}
        <div style="font-size:12px; margin-top:4px;">
            {{ $getState() }}
        </div>
    </div>
@else
    <div style="text-align:center; color: #aaa;">
        No hay código
    </div>
@endif
