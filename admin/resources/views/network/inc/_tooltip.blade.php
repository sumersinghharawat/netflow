@forelse ($tooltipData as $item)
    <div data-serialtip-target="example-{{$item->id}}" class="serialtip-default" data-username="{{ $item->username }}" id="ttip-{{ $item->id }}">
        <div class="tooltip-head">
            <div class="tooltip_image"><img src="/assets/images/users/avatar-1.jpg" alt=""></div>
            <span class="tootip-username">{{ $item->username }}</span>
            @if($tooltipConfig->contains('slug', 'first-name'))
                <span class="tootltip-fullname">{{ $item->userDetails->name }} {{ $item->userDetails->second_name }}</span>
            @endif
        </div>
        <div class="tooltip-table">
            <table>
                @if ($tooltipConfig->contains('slug', 'join-date'))
                <tr>
                    <td>{{ __('tree.join_date') }}</td><td>{{ $item->date_of_joining }}</td>
                </tr>
                @endif
                @if ($moduleStatus->mlm_plan == "Binary" && $tooltipConfig->contains('slug', 'left'))
                    <tr>
                        <td>{{ __('tree.left') }}</td><td> {{ $item->legDetails->total_left_count ?? 0 }}</td>
                    </tr>
                @endif
                @if ($moduleStatus->mlm_plan == "Binary" && $tooltipConfig->contains('slug', 'right'))
                    <tr>
                        <td>{{ __('tree.right') }}</td><td>{{ $item->legDetails->total_right_count ?? 0 }}</td>
                    </tr>
                @endif
                @if ($moduleStatus->mlm_plan == "Binary" && $tooltipConfig->contains('slug', 'left-carry'))
                    <tr>
                        <td>{{ __('tree.left_carry') }}</td><td>{{ $item->legDetails->total_left_carry ?? 0 }}</td>
                    </tr>
                @endif
                @if ($moduleStatus->mlm_plan == "Binary" && $tooltipConfig->contains('slug', 'right-carry'))
                    <tr>
                        <td>{{ __('tree.right_carry') }}</td><td>{{ $item->legDetails->total_right_carry ?? 0}}</td>
                    </tr>
                @endif
                @if ($tooltipConfig->contains('slug', 'personal-pv'))
                    <tr>
                        <td>{{ __('tree.personal_pv') }}</td><td>{{ $item->personal_pv }}</td>
                    </tr>
                @endif
                @if ($tooltipConfig->contains('slug', 'group-pv'))
                    <tr>
                        <td>{{ __('tree.group_pv') }}</td><td>{{ $item->gpv }}</td>
                    </tr>
                @endif
                @if ($moduleStatus->mlm_plan == "Donation" && $tooltipConfig->contains('slug', 'donation-level') && $item->donation_level)
                    <tr>
                        <td>{{ __('tree.donation_level') }}</td><td>{{ $item->donation_level }}</td>
                    </tr>
                @endif
                @if ($moduleStatus->rank_status && $tooltipConfig->contains('slug', 'rank-status') && $item->rankDetail)
                    <tr>
                        <td colspan="2" style="text-align: center">
                            <span class="rank-name" style="background-color: {{ $item->rankDetail->color }}">
                                {{ $item->rankDetail->name }}
                            </span>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
        <p></p>
    </div>
@empty

@endforelse
