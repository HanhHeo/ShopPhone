@extends('layouts.master')

@section('content')

<div class="main">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="text-center">Đơn hàng</h1>
        </div>
        <!-- <div class="container" style="width:600px"> -->
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">Thêm đơn hàng</div>
                        <div class="panel-body">
                            <form method="post" enctype="multipart/form-data" action="{{route('orders.store')}}">
                                <div class="row" style="margin-right:15px; margin-left:15px">
                                    @csrf
                                    <label>Tên khách hàng:</label>
                                    <select name="user_id">
                                        @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="error-message">
                                        @if ($errors->any())
                                        <p style="color:red">{{ $errors->first('user_id') }}</p>
                                        @endif
                                    </div>
                                    <br> <br>
                                    <label>Trạng thái</label>
                                    <select name="status">
                                        <option>--Chọn--</option>
                                        <option value="0">Đang chờ</option>
                                        <option value="1">Đang vận chuyển</option>
                                        <option value="2">Đã hoàn thành</option>
                                    </select>
                                    <div class="error-message">
                                        @if ($errors->any())
                                        <p style="color:red">{{ $errors->first('status') }}</p>
                                        @endif
                                    </div>
                                    <br> <br> <br>

                                    <br>

                                    <button type="submit" class="btn btn-success"><i class="far fa-save"></i> Tạo</button>
                                    <a class="btn btn-danger" href="{{ route('orders.index') }}"><i class="fas fa-undo"></i> Hủy</a>
                            </form>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection