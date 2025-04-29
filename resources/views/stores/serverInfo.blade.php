@extends("layouts.app")

@section("content")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __("Server Information") }}: {{ $store->url }}</span>
                    <a href="{{ route("stores.show", $store) }}" class="btn btn-sm btn-secondary">{{ __("Back to Store") }}</a>
                </div>

                <div class="card-body">
                    @if($serverInfo)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>{{ __("Operating System") }}</th>
                                        <td>{{ $serverInfo->os_info ?? "N/A" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("PHP Version") }}</th>
                                        <td>{{ $serverInfo->php_version ?? "N/A" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("Composer Version") }}</th>
                                        <td>{{ $serverInfo->composer_version ?? "N/A" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("Redis Version") }}</th>
                                        <td>{{ $serverInfo->redis_version ?? "N/A" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("OpenSearch/Elasticsearch Version") }}</th>
                                        <td>{{ $serverInfo->search_engine_version ?? "N/A" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("MariaDB/MySQL Version") }}</th>
                                        <td>{{ $serverInfo->database_version ?? "N/A" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("RabbitMQ Version") }}</th>
                                        <td>{{ $serverInfo->rabbitmq_version ?? "N/A" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("Other Information") }}</th>
                                        <td><pre>{{ $serverInfo->other_info ?? "N/A" }}</pre></td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("Last Updated") }}</th>
                                        <td>{{ $serverInfo->updated_at->format("Y-m-d H:i:s") }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ __("No server information found for this store.") }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
