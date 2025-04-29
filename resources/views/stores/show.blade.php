@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Store Details') }}: {{ $store->url }}</span>
                    <a href="{{ route('stores.index') }}" class="btn btn-sm btn-secondary">{{ __('Back to Stores') }}</a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>{{ __('Store Information') }}</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>{{ __('URL') }}</th>
                                    <td>{{ $store->url }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Client') }}</th>
                                    <td>{{ $store->client->name }} ({{ $store->client->company_name }})</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Platform') }}</th>
                                    <td>{{ $store->platform_type ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Magento Version') }}</th>
                                    <td>{{ $store->magento_version ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Admin Path') }}</th>
                                    <td>{{ $store->admin_path ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Repository') }}</th>
                                    <td>{{ $store->repository_url ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Developer') }}</th>
                                    <td>{{ $store->developer_name ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Last Check') }}</th>
                                    <td>{{ $store->last_check ? $store->last_check->format('Y-m-d H:i:s') : __('Never') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>{{ __('Quick Stats') }}</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">{{ __('Modules') }}</h5>
                                            <h2 class="display-5">{{ \App\Models\Module::where('store_id', $store->id)->count() }}</h2>
                                            <a href="{{ route('stores.modules', $store) }}" class="btn btn-sm btn-light mt-2">{{ __('View Details') }}</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
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
                                    <div class="card bg-{{ $riskClass }} text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">{{ __('Security') }}</h5>
                                            <h2 class="display-5">{{ $securityStatus['overall_risk'] }}</h2>
                                            <a href="{{ route('stores.security', $store) }}" class="btn btn-sm btn-light mt-2">{{ __('View Details') }}</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">{{ __('Server Info') }}</h5>
                                            <p class="mb-0">{{ __('PHP, MySQL, etc.') }}</p>
                                            <a href="{{ route('stores.serverInfo', $store) }}" class="btn btn-sm btn-light mt-2">{{ __('View Details') }}</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">{{ __('Stats') }}</h5>
                                            <p class="mb-0">{{ __('Orders, Customers') }}</p>
                                            <a href="{{ route('stores.stats', $store) }}" class="btn btn-sm btn-light mt-2">{{ __('View Details') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="storeDetailsTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="modules-tab" data-bs-toggle="tab" data-bs-target="#modules" type="button" role="tab" aria-controls="modules" aria-selected="true">{{ __('Modules') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="server-tab" data-bs-toggle="tab" data-bs-target="#server" type="button" role="tab" aria-controls="server" aria-selected="false">{{ __('Server Info') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">{{ __('Security') }}</button>
                                </li>
                            </ul>
                            <div class="tab-content p-3 border border-top-0 rounded-bottom" id="storeDetailsTabsContent">
                                <div class="tab-pane fade show active" id="modules" role="tabpanel" aria-labelledby="modules-tab">
                                    <h5>{{ __('Recent Modules') }}</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Version') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $modules = \App\Models\Module::where('store_id', $store->id)
                                                        ->orderBy('name')
                                                        ->take(5)
                                                        ->get();
                                                @endphp
                                                
                                                @forelse($modules as $module)
                                                    <tr>
                                                        <td>{{ $module->name }}</td>
                                                        <td>{{ $module->version }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $module->is_active ? 'success' : 'secondary' }}">
                                                                {{ $module->is_active ? __('Active') : __('Inactive') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center">{{ __('No modules found') }}</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="{{ route('stores.modules', $store) }}" class="btn btn-primary">{{ __('View All Modules') }}</a>
                                </div>
                                
                                <div class="tab-pane fade" id="server" role="tabpanel" aria-labelledby="server-tab">
                                    @php
                                        $serverInfo = \App\Models\ServerInfo::where('store_id', $store->id)->first();
                                    @endphp
                                    
                                    @if($serverInfo)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5>{{ __('System Information') }}</h5>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>{{ __('Operating System') }}</th>
                                                        <td>{{ $serverInfo->os_version }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('PHP Version') }}</th>
                                                        <td>{{ $serverInfo->php_version }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('MySQL/MariaDB Version') }}</th>
                                                        <td>{{ $serverInfo->mysql_version }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('Composer Version') }}</th>
                                                        <td>{{ $serverInfo->composer_version }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <h5>{{ __('Services') }}</h5>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>{{ __('Redis') }}</th>
                                                        <td>{{ $serverInfo->redis_version ?: __('Not installed') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('Elasticsearch/OpenSearch') }}</th>
                                                        <td>{{ $serverInfo->elasticsearch_version ?: __('Not installed') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('RabbitMQ') }}</th>
                                                        <td>{{ $serverInfo->rabbitmq_version ?: __('Not installed') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('Web Server') }}</th>
                                                        <td>{{ $serverInfo->web_server ?: __('Unknown') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            {{ __('No server information available for this store.') }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                    <h5>{{ __('Security Status') }}</h5>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="card bg-{{ $riskClass }} text-white">
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
                                    
                                    <a href="{{ route('stores.security', $store) }}" class="btn btn-primary">{{ __('View Full Security Report') }}</a>
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
