@extends('layouts.admin')
@section('content')
    @livewire('admin.user.index')
    <script>
    window.addEventListener('close-modal', event => {
        $('#adduser').modal('hide');
        $('#edituser').modal('hide');
    });
</script>
@endsection

