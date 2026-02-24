@extends('filament::page')

@section('content')
    <div class="space-y-6">
        <x-filament::form wire:submit.prevent="aperturarCaja">
            {{ $this->form }}
            <x-filament::button type="submit" color="primary">
                Aperturar
            </x-filament::button>
        </x-filament::form>
    </div>
@endsection
