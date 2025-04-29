@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">{{ __('Clients') }}</h5>
                                </div>
                                <div class="card-body">
                                    <h2 class="display-4 text-center">{{ \App\Models\Client::count() }}</h2>
                                    <p class="text-center">{{ __('Total Clients') }}</p>
                                    <a href="{{ route('clients.index') }}" class="btn btn-outline-primary btn-block w-100">{{ __('View All Clients') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">{{ __('Stores') }}</h5>
                                </div>
                                <div class="card-body">
                                    <h2 class="display-4 text-center">{{ \App\Models\Store::count() }}</h2>
                                    <p class="text-center">{{ __('Total Stores') }}</p>
                                    <a href="{{ route('stores.index') }}" class="btn btn-outline-success btn-block w-100">{{ __('View All Stores') }}</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">{{ __('Security') }}</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $highRiskStores = \App\Models\Store::whereHas('securityStatuses', function($query) {
                                            $query->where('is_applied', false)
                                                  ->where('risk_score', '>=', 7);
                                        })->count();
                                    @endphp
                                    <h2 class="display-4 text-center">{{ $highRiskStores }}</h2>
                                    <p class="text-center">{{ __('High Risk Stores') }}</p>
                                    <a href="{{ route('security.overview') }}" class="btn btn-outline-danger btn-block w-100">{{ __('View Security Status') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">{{ __('Recent Security Patches') }}</div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @forelse(\App\Models\SecurityPatch::orderBy('release_date', 'desc')->take(5)->get() as $patch)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $patch->patch_name }}
                                                <span class="badge bg-{{ $patch->severity_level == 'severe' ? 'danger' : ($patch->severity_level == 'critical' ? 'warning' : 'info') }} rounded-pill">
                                                    {{ $patch->severity_level }}
                                                </span>
                                            </li>
                                        @empty
                                            <li class="list-group-item">{{ __('No security patches found') }}</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">{{ __('Recent Stores') }}</div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        @forelse(\App\Models\Store::orderBy('created_at', 'desc')->take(5)->get() as $store)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $store->url }}
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $store->platform_type ?? 'Unknown' }}
                                                </span>
                                            </li>
                                        @empty
                                            <li class="list-group-item">{{ __('No stores found') }}</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
