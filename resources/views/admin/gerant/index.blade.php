@extends('layouts.admin')
@section('content')
    @livewire('admin.gerant.index')
@endsection
<script>
    window.addEventListener('close-modal', event => {
        $('#addgerant').modal('hide');
        $('#editgerant').modal('hide');
    });
</script>
