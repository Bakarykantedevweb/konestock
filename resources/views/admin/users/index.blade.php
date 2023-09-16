@extends('layouts.admin')
@section('content')
    @livewire('admin.user.index')
@endsection
<script>
    window.addEventListener('close-modal', event => {
        $('#adduser').modal('hide');
        $('#edituser').modal('hide');
    });
</script>
