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
                        {{Form::open(['action'=>'\Motwreen\Translation\Http\Controllers\TranslationController@store'])}}

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    {{Form::text('name',null,['class'=>'form-control','id'=>'name','required'])}}
                                    <small class="text-danger">{{$errors->first('name')}}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="iso">iso</label>
                                    {{Form::text('iso',null,['class'=>'form-control','id'=>'iso','maxlength'=>'3','required'])}}
                                    <small class="text-danger">{{$errors->first('iso')}}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
