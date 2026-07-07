@extends('layouts.app')

@section('content')
    <section class="space-y-6">
        @hasSection('overview')
            <div class="grid gap-6">
                @yield('overview')
            </div>
        @endif

        @hasSection('dashboard-content')
            @yield('dashboard-content')
        @else
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <x-card title="Total Products" value="120" description="Live inventory catalog" />
                <x-card title="Total Stock" value="1500" description="Available units across batches" />
                <x-card title="Expiring Soon" value="5" description="Batches needing attention" />
            </div>
        @endif
    </section>
@endsection
