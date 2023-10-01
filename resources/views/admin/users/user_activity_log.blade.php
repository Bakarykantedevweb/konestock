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
                            <h4>Activités des Administrateurs</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-1">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Auteur</th>
                                            <th>Objet</th>
                                            <th>Evenement</th>
                                            <th>Date d'evenement</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($activityLog as $key => $item)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $item->user->name }}</td>
                                                <td>{{ $item->controller }}</td>
                                                <td>{{ $item->action }}</td>
                                                <td>{{ $item->created_at }}</td>
                                            </tr>
                                        @endforeach
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
