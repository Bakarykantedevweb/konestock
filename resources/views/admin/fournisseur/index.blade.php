@extends('layouts.admin')
@section('content')
    @livewire('admin.fournisseur.index')
    <script>
    window.addEventListener('close-modal', event => {
        $('#addFournisseur').modal('hide');
        $('#editFournisseur').modal('hide');
    });
</script>
@endsection
