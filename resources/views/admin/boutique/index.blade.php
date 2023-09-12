@extends('layouts.admin')
@section('content')
    @livewire('admin.boutique.index')
@endsection
<script>
    window.addEventListener('close-modal', event => {
        $('#addboutique').modal('hide');
        $('#editboutique').modal('hide');
    });
</script>
