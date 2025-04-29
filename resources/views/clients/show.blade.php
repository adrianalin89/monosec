@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Client Details') }}: {{ $client->name }}</span>
                        <a href="{{ route('clients.index') }}" class="btn btn-sm btn-secondary">{{ __('Back to Clients') }}</a>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>{{ __('Client Information') }}</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <td>{{ $client->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <td>{{ $client->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Company Name') }}</th>
                                        <td>{{ $client->company_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Has Credentials') }}</th>
                                        <td>{{ $client->has_credentials ? __('Yes') : __('No') }}</td>
                                    </tr>
                                </table>
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">{{ __('Edit Client') }}</a>
                            </div>
                            <div class="col-md-6">
                                <h5>{{ __('API Key') }}</h5>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="{{ $client->api_key }}" readonly id="apiKeyInput">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey()">{{ __('Copy') }}</button>
                                </div>
                                <form action="{{ route('clients.regenerateApiKey', $client) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to regenerate the API key? The old key will stop working immediately.') }}');">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">{{ __('Regenerate API Key') }}</button>
                                </form>
                            </div>
                        </div>

                        <hr>

                        <h5>{{ __('Associated Stores') }}</h5>
                        @if($client->stores->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{ __('URL') }}</th>
                                        <th>{{ __('Platform') }}</th>
                                        <th>{{ __('Magento Version') }}</th>
                                        <th>{{ __('Last Check') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($client->stores as $store)
                                        <tr>
                                            <td>{{ $store->url }}</td>
                                            <td>{{ $store->platform_type ?? 'N/A' }}</td>
                                            <td>{{ $store->magento_version ?? 'N/A' }}</td>
                                            <td>{{ $store->last_check ? $store->last_check->format('Y-m-d H:i:s') : __('Never') }}</td>
                                            <td>
                                                <a href="{{ route('stores.show', $store) }}" class="btn btn-sm btn-info">{{ __('View Details') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                {{ __('This client does not have any associated stores yet.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyApiKey() {
            var apiKeyInput = document.getElementById("apiKeyInput");
            apiKeyInput.select();
            apiKeyInput.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");
            alert("{{ __('API Key copied to clipboard!') }}");
        }
    </script>
@endsection

