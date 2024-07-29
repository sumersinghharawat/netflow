<thead>
    <th>#</th>
    <th>{{ __('rank.name') }}</th>
    @if ($activeConfig->contains('slug', 'joiner-package'))
        <th>{{ __('rank.package_name') }}</th>
    @else
        @if ($activeConfig->contains('slug', 'referral-count'))
            <th>{{ __('rank.referral-count') }}</th>
        @endif
        @if ($activeConfig->contains('slug', 'personal-pv'))
            <th>{{ __('rank.personal-pv') }}</th>
        @endif
        @if ($activeConfig->contains('slug', 'group-pv'))
            <th>{{ __('rank.group-pv') }}</th>
        @endif
        @if ($activeConfig->contains('slug', 'downline-member-count'))
            <th>{{ __('rank.downline-member-count') }}</th>
        @endif
        @if ($activeConfig->contains('slug', 'downline-package-count'))
            <th>{{ __('rank.downline-package-count') }}</th>
        @endif
        @if ($activeConfig->contains('slug', 'downline-rank-count'))
            <th>{{ __('rank.downline-rank-count') }}</th>
        @endif
    @endif
    <th>{{ __('rank.commission') }}</th>
    <th>{{ __('rank.color') }}</th>
    <th>{{ __('rank.badge') }}</th>
    <th>{{ __('common.action') }}</th>
</thead>
<tbody>
    @foreach ($ranks as $rank)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $rank->name }}</td>
            @if ($activeConfig->contains('slug', 'joiner-package'))
                <td>
                    {{ $rank->rankCriteria->name ?? '' }}
                </td>
            @else
                @if ($activeConfig->contains('slug', 'referral-count'))
                    <td>
                        {{ $rank->rankCriteria->referral_count ?? '' }}
                    </td>
                @endif
                @if ($activeConfig->contains('slug', 'personal-pv'))
                    <td>
                        {{ $rank->rankCriteria->personal_pv ?? '' }}
                    </td>
                @endif
                @if ($activeConfig->contains('slug', 'group-pv'))
                    <td>
                        {{ $rank->rankCriteria->group_pv ?? '' }}
                    </td>
                @endif
                @if ($activeConfig->contains('slug', 'downline-member-count'))
                    <td>
                        {{ $rank->rankCriteria->downline_count ?? '' }}
                    </td>
                @endif

                @if ($activeConfig->contains('slug', 'downline-package-count'))
                    <td>
                        @forelse($rank->downinePackCount as $downlinePack)
                            <li>
                                {{ $downlinePack->name }} : {{ $downlinePack->pivot->count }}
                            </li>
                        @empty
                            <small>Not configured !</small>
                        @endforelse
                    </td>
                @endif
                @if ($activeConfig->contains('slug', 'downline-rank-count'))
                    <td>
                        @forelse($rank->downlineRankCount as $downlineRank)
                            <li>
                                {{ $downlineRank->name }} : {{ $downlineRank->pivot->count }}
                            </li>

                        @empty
                            <small>{{ __('common.not_configured') }}</small>
                        @endforelse
                    </td>
                @endif
            @endif

            <td>
                {{ $currency . ' ' . formatCurrency($rank->commission) }}
            </td>
            <td><button type="button" style="background-color: {{ $rank->color }}"
                    class="img-thumbnail rounded-circle avatar-sm"></button>
            </td>
            <td>
                {{-- @if (isFileExists($rank->image)) --}}
                <img src="{{ $rank->image }}" class="img-thumbnail rounded-circle avatar-sm">
                {{-- @endif --}}
            </td>
            <td>
                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                    <input class="form-check-input" type="checkbox" value="{{ $rank->id }}"
                        data-id="{{ $rank->id }}" id="SwitchCheckSizemd"
                        @if ($rank->status == 'Active') checked @endif onchange="toggleRank(this)">
                    @if ($rank->status == 'Active')
                        <a class="btn btn-outline-primary btn-sm edit" id="edit-btn-{{ $rank->id }}" title="Edit"
                            href="{{ route('rank.edit', $rank->id) }}" onclick="editRank(this)">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                    @endif
                </div>

            </td>

        </tr>
    @endforeach

</tbody>
