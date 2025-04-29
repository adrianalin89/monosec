@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Security Status') }}: {{ $store->url }}</span>
                    <a href="{{ route('stores.show', $store) }}" class="btn btn-sm btn-secondary">{{ __('Back to Store') }}</a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-{{ $securityStatus['overall_risk'] == 'High' ? 'danger' : ($securityStatus['overall_risk'] == 'Medium' ? 'warning' : 'success') }} text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ __('Overall Risk') }}</h5>
                                    <h2 class="display-5">{{ $securityStatus['overall_risk'] }}</h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ __('Pending Patches') }}</h5>
                                    <h2 class="display-5">{{ $securityStatus['pending_patches'] }}</h2>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ __('High Risk Patches') }}</h5>
                                    <h2 class="display-5">{{ $securityStatus['high_risk_patches'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4>{{ __('Security Patches') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Patch Name') }}</th>
                                    <th>{{ __('Release Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Severity') }}</th>
                                    <th>{{ __('Score') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($securityStatus['security_statuses'] as $status)
                                    <tr>
                                        <td>{{ $status->securityPatch->patch_name }}</td>
                                        <td>{{ $status->securityPatch->release_date ? date('Y-m-d', strtotime($status->securityPatch->release_date)) : 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $status->securityPatch->type == 'security' ? 'danger' : 'info' }}">
                                                {{ $status->securityPatch->type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $status->securityPatch->severity_level == 'severe' ? 'danger' : ($status->securityPatch->severity_level == 'critical' ? 'warning' : 'info') }}">
                                                {{ $status->securityPatch->severity_level }}
                                            </span>
                                        </td>
                                        <td>{{ $status->securityPatch->severity_score }}</td>
                                        <td>
                                            @if($status->is_applied)
                                                <span class="badge bg-success">{{ __('Applied') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$status->is_applied)
                                                <form action="{{ route('security.markApplied', ['store' => $store->id, 'status' => $status->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">{{ __('Mark as Applied') }}</button>
                                                </form>
                                            @else
                                                <span class="text-muted">{{ __('No action needed') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No security patches found for this store') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h4 class="mt-4">{{ __('Magento Version Information') }}</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('Current Version') }}</th>
                                    <td>{{ $store->magento_version ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Platform Type') }}</th>
                                    <td>{{ $store->platform_type ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Latest Security Patch') }}</th>
                                    <td>
                                        @php
                                            $latestAppliedPatch = $securityStatus['security_statuses']->where('is_applied', true)->sortByDesc(function($status) {
                                                return $status->securityPatch->release_date ?? '0000-00-00';
                                            })->first();
                                        @endphp
                                        
                                        @if($latestAppliedPatch)
                                            {{ $latestAppliedPatch->securityPatch->patch_name }} ({{ $latestAppliedPatch->securityPatch->release_date ? date('Y-m-d', strtotime($latestAppliedPatch->securityPatch->release_date)) : 'N/A' }})
                                        @else
                                            {{ __('None applied') }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h5>{{ __('Security Recommendations') }}</h5>
                                <ul>
                                    @if($securityStatus['high_risk_patches'] > 0)
                                        <li>{{ __('Apply high risk security patches immediately') }}</li>
                                    @endif
                                    
                                    @if($securityStatus['pending_patches'] > 0)
                                        <li>{{ __('Schedule application of remaining security patches') }}</li>
                                    @endif
                                    
                                    @if($store->magento_version && version_compare($store->magento_version, '2.4.0', '<'))
                                        <li>{{ __('Consider upgrading to Magento 2.4 or newer for improved security features') }}</li>
                                    @endif
                                    
                                    <li>{{ __('Regularly check for new security updates') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
