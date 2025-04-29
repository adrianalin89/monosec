@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Stores') }}</span>
                    <a href="{{ route('stores.create') }}" class="btn btn-sm btn-primary">{{ __('Add New Store') }}</a>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <form action="{{ route('stores.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="url" class="form-control" placeholder="{{ __('Search by URL') }}" value="{{ request('url') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="platform_type" class="form-select">
                                    <option value="">{{ __('All Platforms') }}</option>
                                    <option value="magento_ce" {{ request('platform_type') == 'magento_ce' ? 'selected' : '' }}>{{ __('Magento CE') }}</option>
                                    <option value="mage-os" {{ request('platform_type') == 'mage-os' ? 'selected' : '' }}>{{ __('Mage-OS') }}</option>
                                    <option value="magento_ee" {{ request('platform_type') == 'magento_ee' ? 'selected' : '' }}>{{ __('Magento EE') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="company" class="form-control" placeholder="{{ __('Search by company') }}" value="{{ request('company') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('URL') }}</th>
                                    <th>{{ __('Client') }}</th>
                                    <th>{{ __('Platform') }}</th>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Security Status') }}</th>
                                    <th>{{ __('Last Check') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stores as $store)
                                    <tr>
                                        <td>{{ $store->id }}</td>
                                        <td>{{ $store->url }}</td>
                                        <td>{{ $store->client->name }} ({{ $store->client->company_name }})</td>
                                        <td>{{ $store->platform_type ?? 'Unknown' }}</td>
                                        <td>{{ $store->magento_version ?? 'Unknown' }}</td>
                                        <td>
                                            @php
                                                $securityController = new \App\Http\Controllers\SecurityMonitorController();
                                                $securityStatus = $securityController->getStoreSecurityStatus($store->id);
                                                $riskClass = 'success';

                                                if ($securityStatus['overall_risk'] == 'High') {
                                                    $riskClass = 'danger';
                                                } elseif ($securityStatus['overall_risk'] == 'Medium') {
                                                    $riskClass = 'warning';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $riskClass }}">
                                                {{ $securityStatus['overall_risk'] }}
                                            </span>
                                            @if($securityStatus['pending_patches'] > 0)
                                                <span class="badge bg-secondary">{{ $securityStatus['pending_patches'] }} {{ __('pending') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $store->last_check ? $store->last_check->diffForHumans() : __('Never') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('stores.show', $store) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                                                <a href="{{ route('stores.modules', $store) }}" class="btn btn-sm btn-secondary">{{ __('Modules') }}</a>
                                                <a href="{{ route('stores.security', $store) }}" class="btn btn-sm btn-{{ $riskClass }}">{{ __('Security') }}</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('No stores found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $stores->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
