@extends("layouts.app")

@section("content")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __("Store Statistics") }}: {{ $store->url }}</span>
                    <a href="{{ route("stores.show", $store) }}" class="btn btn-sm btn-secondary">{{ __("Back to Store") }}</a>
                </div>

                <div class="card-body">
                    @if($storeStats)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>{{ __("Customer Count") }}</th>
                                        <td>{{ number_format($storeStats->customer_count) ?? "N/A" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("Order Count") }}</th>
                                        <td>{{ number_format($storeStats->order_count) ?? "N/A" }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __("Last Updated") }}</th>
                                        <td>{{ $storeStats->updated_at->format("Y-m-d H:i:s") }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ __("No statistics found for this store.") }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
