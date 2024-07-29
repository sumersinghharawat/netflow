@extends('layouts.app')
<style>

    .card-stair-step{
        width: 100%;
        height: 100px;
        float: left;
        display: flex;
        gap: 10px;
        margin-bottom: 20px;



    }
    .staristep-card{
        width: 100%;
    }
    .card-stair-step span{
        align-content: center;
        justify-content: center;
        word-wrap: break-word;
        /* display: flex; */
        flex-direction: column;
        position: relative;
    }
    .right_step_name{
        width: 100%;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .step_hover_box{
        width: 300px;
        height: 400px;
        position: absolute;
        right: 0;
        left: 0;
        margin: auto;
        top:auto;
        bottom:100px;
        background-color: #fff;
        display: none;
        box-shadow: 0px 0px 20px #ccc;
        z-index: 99;
    }
    .card-stair-step span:hover .step_hover_box{display: block}
</style>
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __("tree.step_view") }}</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex justify-content-end">
                    <div class="col-md-3">
                        <form action="{{ route('network.step.view') }}">
                            <div class="tree_view_right_srch_sec">
                                <select name="user" class="form-control treeview_frm_input select2-search-user">
                                    @isset($user)
                                        <option selected value="{{ $user->id }}">{{ $user->username }}</option>
                                    @endisset
                                </select>
                                <span>
                                    <div class="form-group m-b-n-xs">
                                        <button class="btn btn-sm btn-primary treeview_srch_btn">Search</button>
                                    </div>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @foreach ($maxStep as $k => $stepDetails)
        @php
            $width      = ($k+1) * 200;
        @endphp
        <div class="row">
            <div class="col-md-8">
                <div class="staristep-card " style="width:{{ $width }}px;">
                    <div class="card-stair-step @if(!$stepArray->contains('step', $stepDetails->id)) btn-primary @endif">
                        @foreach ($stepArray as $k => $item)
                            @if ($item['step'] == $stepDetails->id)
                            @php
                                $spanCount  = $stepArray->where('step', $stepDetails->id)->count();
                                $spanWidth  = ($width/$spanCount) - ($spanCount * 5);
                            @endphp
                            <span class="btn btn-primary" style="width:{{ $spanWidth }}px;">
                                {{ $item['username'] }}
                                <div class="step_hover_box">
                                    <table>
                                        <tr>
                                            <td colspan="2">{{ $tooltipArray[$k]['user_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">{{ $tooltipArray[$k]['full_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('tree.joining_date') }}</td>
                                            <td>{{ $tooltipArray[$k]['date_of_joining']  }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('common.personal_pv') }}</td>
                                            <td>{{ $tooltipArray[$k]['personal_pv']  }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ __('common.group_pv') }}</td>
                                            <td>{{ $tooltipArray[$k]['group_pv']  }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <span class="right_step_name">{{ $stepDetails->name }} </span>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <script>
        $( () => {
            getUsers();
        });
    </script>
@endpush
