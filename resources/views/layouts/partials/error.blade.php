<div>
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible show fade">
            <div class="alert-body">
                {{ session('error') }}.
            </div>
        </div>
    @endif
</div>
