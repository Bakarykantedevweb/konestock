@extends('layouts.admin')
@section('content')
    @livewire('admin.gerant.index')
    <script>
    window.addEventListener('close-modal', event => {
        $('#addgerant').modal('hide');
        $('#editgerant').modal('hide');
    });
</script>
@endsection
