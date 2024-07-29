@extends('layouts.replica')
@section('content')
<section class="sub-container">
    <div class="container">
        <table>
            <tr>
                <td>
                    <!-- Start plan content  -->
                        <h1 class="heading">{lang('privacy_policy')}</h1>
                        @isset($replicacontent['policy'])
                                <p>{!! $replicacontent['policy'] !!}</p>
                                @endisset
                    <!-- Close plan content  -->
                </td>
            </tr>
        </table>
    </div>
</section>
@endsection
