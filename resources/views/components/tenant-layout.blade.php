@extends('tenant.layouts.app')

@section('content')
    @isset($header)
        <div class="mb-6">
            <h1 style="font-size:20px;font-weight:600;color:var(--color-text-primary);">
                {{ $header }}
            </h1>
        </div>
    @endisset

    {{ $slot }}
@endsection