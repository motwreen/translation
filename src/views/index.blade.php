@extends('Translation::app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="col-md-12 text-right">
                            <a href="{{route('translation.create')}}" class="btn btn-success">Add New</a>
                            <br>
                            <br>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <th>#</th>
                                <th>Name</th>
                                <th>iso code</th>
                                <th>action</th>
                            </thead>
                            <tbody>
                                @foreach($locales as $locale)
                                    <tr>
                                        <td>{{$locale->id}}</td>
                                        <td>{{$locale->name}}</td>
                                        <td>{{$locale->iso}}</td>
                                        <td>
                                            {{Form::open(['route'=>['translation.destroy',$locale],'method'=>'delete'])}}
                                                <a href="{{route('translation.destroy',[$locale])}}" class="btn btn-success"> <i class="fa fa-eye"></i> Show</a>
                                                @if(!$locale->default)
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to delete ?')"> <i class="fa fa-times"></i> Delete</button>
                                                @endif
                                            {{Form::close()}}
                                        </td>
                                    </tr>
                                @endforeach
                            {{$locales->render()}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
