@extends('Translation::app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Dashboard</div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                    {{Form::open(['route'=>['translation.save_translations',$locale]])}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    {{Form::select('file',$files_in_dir,request()->get('file'),['class'=>'form-control','placeholder'=>'Please Select ...'])}}
                                    <small class="text-danger">{{$errors->first('name')}}</small>
                                </div>
                            </div>
                            <div class="col-md-12" id="new_file_name" style="display: none;">
                                <div class="form-group">
                                    <label for="name">New File Name</label>
                                    {{Form::text('new_file_name',null,['class'=>'form-control','placeholder'=>'Enter new file name ...'])}}
                                    <small class="text-danger">{{$errors->first('new_file_name')}}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @if($locale->iso != config('app.locale'))
                                <div class="col-md-6">
                                    <h6>English</h6>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <h6>{{$locale->name}}</h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row col-md-12" id="inputs_wrapper"></div>
                            <div id="addKeyWrapper" class="row col-md-12">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">New Key</label>
                                        {{Form::text('',null,['class'=>'form-control','id'=>'index_key'])}}
                                    </div>
                                </div>
                                @if($locale->iso != config('app.locale'))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">English Value</label>
                                        {{Form::text('',null,['class'=>'form-control','id'=>'index_value_en'])}}
                                    </div>
                                </div>
                                @endif

                                <div class="col-md-{{($locale->iso == config('app.locale'))?"8":"4"}}">
                                    <div class="form-group">
                                        <label for="">{{$locale->name}} Value</label>
                                        {{Form::text('',null,['class'=>'form-control','id'=>'index_value_other'])}}
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="">Add Key</label>
                                        <button class="btn btn-success" type="button" id="addNewKey"><i class="fa fa-plus"></i> Add Key</button>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
@section('script')
    <script>
        $(document).ready(function () {
            readFile("{{$locale->iso}}",$('select[name="file"]').val());

            $('select[name="file"]').on('change',function () {
               readFile("{{$locale->iso}}",$(this).val());
            });

            $('input[name="new_file_name"]').on('change',function () {
                $.ajax({
                    url: "{{route('translation.validate_file_name')}}?lang={{$locale->iso}}&new_file_name="+$(this).val(),
                    beforeSend: function( xhr ) {
                        xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
                    }
                }).done(function( data ) {
                    if(JSON.parse(data).success !== true){
                        $('button[type="submit"]').attr('disabled',true);
                        return $('input[name="new_file_name"]').parent().find('small').text(JSON.parse(data).errors[0]);
                    }else{
                        $('button[type="submit"]').attr('disabled',false);
                        return $('input[name="new_file_name"]').parent().find('small').text("");
                    }
                });
            });


            $('#addNewKey').on('click',function () {
                $key = $('#index_key');
                $en = $('#index_value_en');
                $other = $('#index_value_other');
                if($key.val().trim() === ""){
                    return false;
                }

                @if($locale->iso != config('app.locale'))
                    newKeyValue($key.val(),$en.val(),'default',$('#inputs_wrapper'));
                @endif
                newKeyValue($key.val(),$other.val(),'other',$('#inputs_wrapper'));

                $key.val('');
                $en.val('');
                $other.val('');
            });

            function readFile($lang,$file_name)
            {
                $('#inputs_wrapper').empty();

                if($file_name === ""){
                    $("#new_file_name").hide();
                    return $('#addKeyWrapper').hide();
                }
                if($file_name === "new_file"){
                    $("#new_file_name").show();
                    return $('#addKeyWrapper').show();
                }else{
                    $("#new_file_name").hide();
                    $('#addKeyWrapper').show();
                }

                $.ajax({
                    url: "{{route('translation.ajax_read_file')}}/?locale="+$lang+"&file="+$file_name,
                    beforeSend: function( xhr ) {
                        xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
                    }
                }).done(function( data ) {
                    return convertDataToInputs(JSON.parse(data));
                });

            }

            function convertDataToInputs(data) {
                $.each(data,function ($key,$value) {
                    @if($locale->iso != config('app.locale'))
                    inputToDom($key, $value['default'],$('#inputs_wrapper'));
                    @endif
                    inputToDom($key, $value['other'],$('#inputs_wrapper'));
                });

            }

            function newKeyValue($key,$value,$lang,$where) {
                $key = 'newkey.'+$lang+'.'+$key;
                return inputToDom($key,$value,$where,0)
            }


            function inputToDom($key,$value,$where,disabled) {
                if(disabled===1){
                    $input =
                    '<div class="col-md-{{($locale->iso == config('app.locale'))?"12":"6"}}">' +
                        '<div class="form-group">' +
                            '<label for="'+$key+'">'+$key.trim("newkey.en")+'</label>' +
                            '<input class="form-control" id="'+$key+'" disabled="disabled" name="'+$key+'" type="text" value="'+$value+'">'+
                            '<small class="text-danger"></small>'+
                        '</div>'+
                    '</div>';
                }else{
                    $input =
                    '<div class="col-md-{{($locale->iso == config('app.locale'))?"12":"6"}}">\n' +
                        '<div class="form-group">\n' +
                            '<label for="'+$key+'">'+$key.replace("newkey.default.","").replace("newkey.other.","")+'</label>\n' +
                            '<input class="form-control" id="'+$key+'" name="'+$key+'" type="text" value="'+$value+'">\n' +
                            '<small class="text-danger"></small>\n' +
                        '</div>'+
                    '</div>';
                }
                $where.append($input);
            }

        });
    </script>
@endsection
