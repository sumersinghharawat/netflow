@extends('layouts.app')
@section('title', 'Menu-Permission')
@section('content')
    <style>
        .responsive-tbl-view{
            width: 100%;
            float: left;
            overflow: auto
        }
        .border-view-tbl {
            background-color: #fff;
            width: 100%;
            float: left;
            padding: 10px;
            min-width: 1200px;
        }
        .border-view-tbl .col-12{gap: 10px; border: 1px #ccc solid;align-items: center;margin: 10px 0;padding:0 5px}
        .border-view-tbl .col {

            padding: 5px 0
        }
        .border-view-tbl .col:first-child{max-width: 50px;}

        .config_menu_tbl_cnt {
            width: 100%;
            float: left;
        }

        .config_menu_tbl_sub_cnt {
            width: 100%;
            float: left;
            padding-left: 20px;

        }
        .config_menu_tbl_head{
            width: 100%;
            float: left;
        }
        .config_menu_tbl_head .col-12{margin-bottom: 0}
        .config_menu_tbl_sub_cnt .col-12{background-color: #f3f3f3;padding: 0 3px;border: solid 1px #ccc;margin: 0;margin-top: -10px}
    </style>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Menu Permission</h4>
                <button class="btn btn-sm btn-primary">Add</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="responsive-tbl-view">
            <div class="border-view-tbl">
                <div class="config_menu_tbl_head">
                <div class=" mb-1">
                    <div class="col-12 d-flex ">
                        <div class="col"><strong>Sl. No</strong></div>
                        <div class="col"><strong>Menu</strong></div>
                        <div class="col"><strong>Slug</strong></div>
                        <div class="col"><strong>Route Name</strong></div>
                        <div class="col"><strong>Menu Order</strong></div>
                        <div class="col"><strong>Admin Icon</strong></div>
                        <div class="col"><strong>User Icon</strong></div>
                        {{-- <div class="col"><strong>Mobile Icon</strong></div> --}}
                        <div class="col"><strong>Admin Permisssion</strong></div>
                        <div class="col"><strong>User Permisssion</strong></div>
                        <div class="col"><strong>Admin Only</strong></div>
                        <div class="col"><strong>User Only</strong></div>
                        <div class="col"><strong>Action</strong></div>
                    </div>
                </div>
            </div>

                    <div class="config_menu_tbl_cnt">
                        @forelse ($menus->where('side_menu', 1) as $menu)
                        <form action="{{ route('menu.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="menu" value="{{ $menu->id }}">
                            <div class="col-12 d-flex">
                                <div class="col">{{ $loop->index + 1 }}</div>
                                <div class="col">{{ $menu->title }}</div>
                                <div class="col">{{ $menu->slug }}</div>
                                <div class="col">{{ $menu->route_name }}</div>
                                <div class="col">
                                    <input type="text" name="order" class="form-control" value="{{ $menu->order }}">
                                </div>
                                <div class="col">
                                    <div class="input-group">
                                        <span class="input-group-text" id="option-date"><i
                                                class="{{ $menu->admin_icon }}"></i></span>
                                        <input type="text" name="admin_icon" class="form-control"
                                            value="{{ $menu->admin_icon }}">
                                    </div>
                                </div>
                                <div class="col">
                                    <input type="text" name="user_icon" class="form-control"
                                        value="{{ $menu->user_icon }}">
                                </div>
                                {{-- <div class="col">
                                    <input type="text" name="admin_icon" class="form-control"
                                        value="{{ $menu->mobile_icon }}">
                                </div> --}}
                                <div class="col">
                                    <div class="form-check form-switch form-switch-md float-end" dir="ltr">
                                        <input class="form-check-input" name="admin_permission" type="checkbox" value="1"
                                            id="SwitchCheckSizemd" @checked($menu->permission->admin_permission)>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check form-switch form-switch-md float-end" dir="ltr">
                                        <input class="form-check-input" name="user_permission" type="checkbox" value="1"
                                            id="SwitchCheckSizemd" @checked($menu->permission->user_permission)>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check form-switch form-switch-md float-end" dir="ltr">
                                        <input class="form-check-input" name="admin_only" type="checkbox" value="1"
                                            id="SwitchCheckSizemd" @checked($menu->admin_only)>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-check form-switch form-switch-md float-end" dir="ltr">
                                        <input class="form-check-input" name="react_only" type="checkbox" value="1"
                                            id="SwitchCheckSizemd" @checked($menu->react_only)>
                                    </div>
                                </div>
                                <div class="col">
                                    <button class="btn btn-sm btn-primary">Save</button>
                                </div>

                            </div>
                        </form>

                            @forelse ($menu->children->sortBy('child_order') as $child)
                            <form action="{{ route('menu.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="menu" value="{{ $child->id }}">
                                <div class="config_menu_tbl_sub_cnt">
                                    <div class="col-12 d-flex">
                                        <div class="col">{{ $loop->index + 1 }}</div>
                                        <div class="col">{{ $child->title }}</div>
                                        <div class="col">{{ $child->slug }}</div>
                                        <div class="col">
                                            <input type="text" name="route_name" class="form-control" value="{{ $child->route_name }}">
                                        </div>
                                        <div class="col">
                                            <input type="text" name="child_order" class="form-control" value="{{ $child->child_order }}">
                                        </div>
                                        <div class="col">

                                        </div>
                                        <div class="col">

                                        </div>
                                        <div class="col">

                                        </div>
                                        <div class="col">
                                            <div class="form-check form-switch form-switch-md" dir="ltr">
                                                <input class="form-check-input" name="admin_permission" type="checkbox" value="{{ $child->id }}"
                                                    id="SwitchCheckSizemd" @checked($child->permission->admin_permission)>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-check form-switch form-switch-md" dir="ltr">
                                                <input class="form-check-input" name="user_permission" type="checkbox" value="{{ $child->id }}"
                                                    id="SwitchCheckSizemd" @checked($child->permission->user_permission)>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-check form-switch form-switch-md float-end" dir="ltr">
                                                <input class="form-check-input" name="admin_only" type="checkbox" value="{{ $child->id }}"
                                                    id="SwitchCheckSizemd" @checked($child->admin_only)>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-check form-switch form-switch-md float-end" dir="ltr">
                                                <input class="form-check-input" name="react_only" type="checkbox" value="{{ $child->id }}"
                                                    id="SwitchCheckSizemd" @checked($child->react_only)>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-sm btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @empty
                            @endforelse
                        @empty
                        @endforelse

                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection
