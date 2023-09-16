<div>
    @if (session('message'))
        <div class="alert alert-success alert-dismissible show fade">
            <div class="alert-body">
                {{ session('message') }}.
            </div>
        </div>
    @endif
</div>
