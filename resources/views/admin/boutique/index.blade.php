@extends('layouts.admin')
@section('content')
    @livewire('admin.boutique.index')
    <script>
        window.addEventListener('close-modal', event => {
            $('#addboutique').modal('hide');
            $('#editboutique').modal('hide');
        });
    </script>
@endsection
