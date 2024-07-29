@extends('layouts.app')
@section('title', __('tree.tree_view'))
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('tree.tree_view') }}</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex justify-content-end">
                    <div class="col-md-6">
                        <form action="{{ route('network.treeview') }}">
                            <div class="tree_view_right_srch_sec">
                                <span>
                                    <select name="user" class="form-control treeview_frm_input select2-search-user">
                                        @isset($user)
                                            <option selected value="{{ $user->id }}">{{ $user->username }}</option>
                                        @endisset
                                    </select>
                                </span>
                                <span>
                                    <div class="form-group m-b-n-xs">
                                        <button
                                            class="btn btn-sm btn-primary treeview_srch_btn">{{ __('common.search') }}</button>
                                        <a class="btn btn-sm btn-info treeview_rst_btn"
                                            href="{{ route('network.treeview') }}">{{ __('common.reset') }} </a>
                                    </div>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <h4>{{ Str::upper($user->username) }}</h4>
    </div>
    <hr>
    <div class="tree_view_sec" id="tree-view-head">
        {!! $data['view'] !!}
    </div>
@endsection


@push('scripts')
    <link rel="stylesheet" href="{{ asset('assets/css/tooltipster.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/tooltipster.main.css') }}">

    <script src="{{ asset('assets/js/tooltipster.bundle.js') }}"></script>
    <script>
        $(() => {
            $(document).on('click', '.expand', function(event) {
                event.preventDefault()
                $(this).toggleClass('expand shrink')
            })
            getUsers();

            $(document).on('click', '.shrink', function(){
                let element     = $(this);
                let id          = $(this).data('id');
                let parentLi    = $(this).closest('li');
                parentLi.find('ul').hide('slow');
                element.removeClass('shrink').addClass('expand').html(`<i class="fas fa-plus-circle"></i>`);
                element.attr('onclick', `getChild(${id})`);
                setTimeout(() => {
                    parentLi.find('ul').remove();
                }, 1);
            })
            toolTip();
        })

        const getChild = async (id) => {
            $(`#child-${id}`).html(`<i class="fas fa-minus-circle"></i>`).removeAttr('onclick').toggleClass(
                'expand shrink');
            const res = await $.get(`{{ route('tree.get.child') }}`, {
                id
            });
            $(`#tree-child-${id}`).hide().append(res.data.view).show('slow');
            toolTipChildren(res.data.tooltip);
        }

        const toolTip = () => {
            let tooltipData = `<?php print_r($data['tooltip']); ?>`;
            let obj = JSON.parse(tooltipData);
            console.log(Object.entries(obj));
            for (let [key, value] of Object.entries(obj)) {
                $(`#${value.username}-tooltip`).tooltipster({
                    content: $(tootipRender(value))
                });
            }
        }
        const toolTipChildren = (tooltipData) => {
            let obj = JSON.parse(tooltipData)
            obj.forEach(element => {
                $(`#${element.username}-tooltip`).tooltipster({
                    content: $(tootipRender(element))
                });
            });
        }
        const tootipRender = (value) => {
            let img = '';
            if (value.img) {
                img = value.img;
            } else {
                img = `{{ asset('assets/images/users/avatar-1.jpg') }}`;
            }
            console.log(img);
            let toolTipView = `<span class="treeview_tooltip_view_sec">`;
            toolTipView += `<div class="treeview_tooltip_view_head"><div class="treeview_tooltip_view_image"><img width="50px" src="${img}" /></div>
                                <div class="treeview_tooltip_view_head">
                                    ${value.username}
                                </div>
                                <div class="treeview_tooltip_view_text">
                                    ${value.full_name}
                                </div>
                                </div>
                                <table>
                                <tr>
                                    <td>{{ __('tree.joining_date') }}</td>
                                    <td>: ${value.join_date}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('tree.personal_pv') }}</td>
                                    <td>: ${value.personal_pv}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('tree.group_pv') }}</td>
                                    <td>: ${value.gpv}</td>
                                </tr>`;
            if (value.hasOwnProperty('left')) {
                toolTipView += `<tr>
                    <td>{{ __('tree.left') }}</td>
                    <td>: ${value.left}</td>
                </tr>`;
            }
            if (value.hasOwnProperty('right')) {
                toolTipView += `<tr>
                                    <td>{{ __('tree.right') }}</td>
                                    <td>: ${value.right}</td>
                                </tr>`;
            }
            if (value.hasOwnProperty('left_carry')) {
                toolTipView += `<tr>
                                    <td>{{ __('tree.left_carry') }}</td>
                                    <td>: ${value.left_carry}</td>
                                </tr>`;
            }
            if (value.hasOwnProperty('right_carry')) {
                toolTipView += `<tr>
                                    <td>{{ __('tree.right_carry') }}</td>
                                    <td>: ${value.right_carry}</td>
                                </tr>`;
            }
            if (value.hasOwnProperty('rank')) {
                toolTipView += `<tr>
                                    <td colspan="2" class="rank_name">${value.rank.name}</td>
                                    </tr>`;
            }
            return toolTipView;
        }
    </script>
@endpush

