@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Security Overview') }}</span>
                    <form action="{{ route('security.checkUpdates') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">{{ __('Check for Updates') }}</button>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ __('Total Stores') }}</h5>
                                    <h2 class="display-4">{{ $totalStores }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ __('Stores with Issues') }}</h5>
                                    <h2 class="display-4">{{ $storesWithIssues }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ __('High Risk Stores') }}</h5>
                                    <h2 class="display-4">{{ $highRiskStores }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4>{{ __('Recent Security Patches') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Patch Name') }}</th>
                                    <th>{{ __('Release Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Severity') }}</th>
                                    <th>{{ __('Score') }}</th>
                                    <th>{{ __('Affected Versions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPatches as $patch)
                                    <tr>
                                        <td>{{ $patch->patch_name }}</td>
                                        <td>{{ $patch->release_date ? date('Y-m-d', strtotime($patch->release_date)) : 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $patch->type == 'security' ? 'danger' : 'info' }}">
                                                {{ $patch->type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $patch->severity_level == 'severe' ? 'danger' : ($patch->severity_level == 'critical' ? 'warning' : 'info') }}">
                                                {{ $patch->severity_level }}
                                            </span>
                                        </td>
                                        <td>{{ $patch->severity_score }}</td>
                                        <td>{{ $patch->magento_version }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('No security patches found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h4 class="mt-4">{{ __('Stores with Security Issues') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Store') }}</th>
                                    <th>{{ __('Client') }}</th>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Pending Patches') }}</th>
                                    <th>{{ __('High Risk Patches') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $storesWithSecurityIssues = \App\Models\Store::whereHas('securityStatuses', function($query) {
                                        $query->where('is_applied', false);
                                    })->with(['client', 'securityStatuses.securityPatch'])->get();
                                @endphp
                                
                                @forelse($storesWithSecurityIssues as $store)
                                    @php
                                        $securityController = new \App\Http\Controllers\SecurityMonitorController();
                                        $securityStatus = $securityController->getStoreSecurityStatus($store->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $store->url }}</td>
                                        <td>{{ $store->client->name }}</td>
                                        <td>{{ $store->magento_version ?? 'Unknown' }}</td>
                                        <td>{{ $securityStatus['pending_patches'] }}</td>
                                        <td>{{ $securityStatus['high_risk_patches'] }}</td>
                                        <td>
                                            <a href="{{ route('stores.security', $store) }}" class="btn btn-sm btn-info">{{ __('View Details') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('No stores with security issues found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
