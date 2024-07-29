@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="modal-body">
                    <div class="modal-ewallet-area">
                        <h3>{{__('settings.add_purchase_package')}}</h3>
                    </div>
                    <div class="popup-input">
                        <form action="{{route('rePurchaseAddnew')}}" id="form" method="post" accept-charset="utf-8" autocomplete="off" novalidate="novalidate">
                        @csrf

                    <div class="row">
                            <div class="panel panel-default">
                            <div class="col-sm-12 col-xs-12">
                             <div class="form-group ok">
                                <label class="control-label required" for="package_id">{{__('settings.id')}}</label>
                                    <input class="form-control" type="text" name="package_id" id="package_id" value="" autocomplete="off">
                                    <span id="errmsg1"></span>
                                    <span name="form_err"></span>
                            </div>
                            </div>
                            <div class="col-xs-12 col-sm-12">
                                <div class="form-group ok">
                                <label class="control-label required" for="prod_name">{{__('common.name')}}</label>
                                    <input class="form-control" type="text" name="prod_name" id="prod_name" value="" autocomplete="off">
                                    <span name="form_err"></span>
                                </div>
                            </div>

                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label required" for="category">{{__('common.category')}}</label>
                                        <select name="category" id="category" class="form-select">
                                            <option value="default">{{__('common.select_category')}}</option>
                                            @foreach($category as $value)
                                            <option value="{{$value->id}}">{{$value->category_name}}</option>

                                            @endforeach


                                        </select>

                                </div>

                            </div>
                            <div class="col-sm-12 col-xs-12">
                             <div class="form-group ok">
                                <label class="control-label required" for="product_amount">{{__('common.amount')}}</label>
                                    <div class="input-group ">
                                        <span class="input-group-addon">₹ </span>
                                            <input class="form-control" type="number" name="product_amount" id="product_amount" value="" autocomplete="off">

                                            <span id="errmsg1"></span>
                                    </div>
                                    <span name="form_err"></span>
                            </div>
                            </div>
                                                    <div class="col-sm-12 col-xs-12">
                                <div class="form-group ok">
                                    <label class=" control-label required" for="pair_value">{{__('settings.pv')}}</label>
                                        <input class="form-control" type="number" name="pair_value" id="pair_value" value="" autocomplete="off">
                                        <span id="errmsg2"></span>
                                        <span name="form_err"></span>
                                </div>
                            </div>

                                                    <div class="col-sm-12 col-xs-12">
                                <div class="form-group ok">
                                    <label class="control-label required" for="description">{{__('settings.description')}}</label>
                                    <input type="textarea" class="form-control" id="description" name="description" value="" autocomplete="off">

                                </div>

                            </div>
                            {{-- <div class="col-xs-12 col-sm-12">
                                <div class="form-group ok">
                                    <label class="control-label" for="product_id"> {{ __('settings.select_product_image') }}</label>
                                    <div class="fileupload fileupload-new bg_file_upload" data-provides="fileupload">
                                        <div class="fileupload-preview fileupload-exists thumbnail imgpre"></div>
                                        <div class="user-edit-image-buttons">
                                            <span class="btn btn-info selectc_file_height btn-light-grey-up btn-file"><span class="fileupload-new"> {{ __('settings.select_image') }}</span>
                                            <span class="fileupload-exists"><i class="fa fa-picture"></i> {{ __('common.change') }}</span>
                                            <input type="file" id="upload_doc" name="upload_doc" autocomplete="off">
                                            </span>
                                            <a href="#" class="btn fileupload-exists selectc_file_height btn-light-grey-up btn-light-grey " data-dismiss="fileupload">
                                                <i class="fa fa-times"></i>{{ __('common.remove') }}
                                            </a>
                                        </div>
                                            <p id="2" style="color: #31708f;" class="ext form-control-static-2 m-t-xs"> {{ __('common.maximum_size_allowed') }}: 2MB<br>
                                                 File types allowed:  png | jpeg | jpg | gif<br>
                                                 {{ __('common.ideal_image_dimension_for_products') }}: 292 x 164 pixel <br>
                                       </p>
                                    </div>
                                </div>

                            </div> --}}
                            <div class="col-xs-12 col-sm-12">
                                <div class="form-group ok">
                                    <button class="btn btn-sm btn-primary" type="submit" name="submit_prod" id="submit_prod" value="add_product">{{__('settings.add_package')}}</button>
                                </div>

                            </div>




                        </div>
            </div>
        </div>


    </div>

@endsection
