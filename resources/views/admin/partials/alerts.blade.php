@if (session('success'))
    <div class="alert alert-success admin-alert">{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger admin-alert">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
