@extends('layouts.admin')
@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    @include('layouts.partials.message')
                    @include('layouts.partials.error')
                    <div class="card">
                        <div class="card-header">
                            <h4>Liste des sessions</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-1">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;">#</th>
                                            <th>Administrateur</th>
                                            <th>Connexion</th>
                                            <th>Deconnexion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($activityLog as $key => $item)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $item->user->name }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($item->created_at)->locale('fr_FR')->isoFormat('dddd D MMMM YYYY à HH:mm:ss') }}
                                                </td>
                                                <td>
                                                    {{ $item->deconnection }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Pas de Departements</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    {{ $activityLog->links() }}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
