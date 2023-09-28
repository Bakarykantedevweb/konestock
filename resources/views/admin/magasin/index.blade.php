@extends('layouts.admin')
@section('content')
    @livewire('admin.magasin.index')
    <script>
    window.addEventListener('close-modal', event => {
        $('#addmagasin').modal('hide');
        $('#editmagasin').modal('hide');
    });
</script>
@endsection
