@extends("layouts.app")

@section("content")
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __("Add New Store") }}</span>
                    <a href="{{ route("stores.index") }}" class="btn btn-sm btn-secondary">{{ __("Back to Stores") }}</a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route("stores.store") }}">
                        @csrf

                        <div class="mb-3">
                            <label for="client_id" class="form-label">{{ __("Client") }} <span class="text-danger">*</span></label>
                            <select class="form-select @error("client_id") is-invalid @enderror" id="client_id" name="client_id" required>
                                <option value="" disabled selected>{{ __("Select a client") }}</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old("client_id") == $client->id ? "selected" : "" }}>{{ $client->name }} ({{ $client->company_name }})</option>
                                @endforeach
                            </select>
                            @error("client_id")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">{{ __("Store URL") }} <span class="text-danger">*</span></label>
                            <input type="url" class="form-control @error("url") is-invalid @enderror" id="url" name="url" value="{{ old("url") }}" required placeholder="https://www.example.com">
                            @error("url")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="platform_type" class="form-label">{{ __("Platform Type") }}</label>
                            <select class="form-select @error("platform_type") is-invalid @enderror" id="platform_type" name="platform_type">
                                <option value="" selected>{{ __("Select Platform (Optional)") }}</option>
                                <option value="magento_ce" {{ old("platform_type") == "magento_ce" ? "selected" : "" }}>Magento CE</option>
                                <option value="magento_ee" {{ old("platform_type") == "magento_ee" ? "selected" : "" }}>Magento EE</option>
                                <option value="mage-os" {{ old("platform_type") == "mage-os" ? "selected" : "" }}>Mage-OS</option>
                            </select>
                            @error("platform_type")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="magento_version" class="form-label">{{ __("Magento Version") }}</label>
                            <input type="text" class="form-control @error("magento_version") is-invalid @enderror" id="magento_version" name="magento_version" value="{{ old("magento_version") }}" placeholder="e.g., 2.4.6-p5">
                            @error("magento_version")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="admin_path" class="form-label">{{ __("Admin Path") }}</label>
                            <input type="text" class="form-control @error("admin_path") is-invalid @enderror" id="admin_path" name="admin_path" value="{{ old("admin_path") }}" placeholder="e.g., /admin_secretpath">
                            @error("admin_path")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="repo_url" class="form-label">{{ __("Repository URL") }}</label>
                            <input type="url" class="form-control @error("repo_url") is-invalid @enderror" id="repo_url" name="repo_url" value="{{ old("repo_url") }}" placeholder="e.g., https://github.com/user/repo.git">
                            @error("repo_url")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact_details" class="form-label">{{ __("Contact Details") }}</label>
                            <textarea class="form-control @error("contact_details") is-invalid @enderror" id="contact_details" name="contact_details" rows="3">{{ old("contact_details") }}</textarea>
                            @error("contact_details")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="developer_details" class="form-label">{{ __("Developer Details") }}</label>
                            <textarea class="form-control @error("developer_details") is-invalid @enderror" id="developer_details" name="developer_details" rows="3">{{ old("developer_details") }}</textarea>
                            @error("developer_details")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">{{ __("Add Store") }}</button>
                            <a href="{{ route("stores.index") }}" class="btn btn-secondary">{{ __("Cancel") }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
