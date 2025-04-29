@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Clients') }}</span>
                    <a href="{{ route('clients.create') }}" class="btn btn-sm btn-primary">{{ __('Add New Client') }}</a>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <form action="{{ route('clients.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="name" class="form-control" placeholder="{{ __('Search by name') }}" value="{{ request('name') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="email" name="email" class="form-control" placeholder="{{ __('Search by email') }}" value="{{ request('email') }}">
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Company') }}</th>
                                    <th>{{ __('Stores') }}</th>
                                    <th>{{ __('Credentials') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>{{ $client->id }}</td>
                                        <td>{{ $client->name }}</td>
                                        <td>{{ $client->email }}</td>
                                        <td>{{ $client->company_name }}</td>
                                        <td>{{ $client->stores_count ?? $client->stores->count() }}</td>
                                        <td>
                                            @if($client->has_credentials)
                                                <span class="badge bg-success">{{ __('Yes') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this client?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No clients found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
