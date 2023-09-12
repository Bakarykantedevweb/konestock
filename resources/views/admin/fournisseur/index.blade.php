@extends('layouts.admin')
@section('content')
    @livewire('admin.fournisseur.index')
@endsection
<script>
    window.addEventListener('close-modal', event => {
        $('#addFournisseur').modal('hide');
        $('#editFournisseur').modal('hide');
    });
</script>
