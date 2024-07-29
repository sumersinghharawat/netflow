 <div class="offcanvas offcanvas-end" tabindex="-1" id="editRank" aria-labelledby="offcanvasExampleLabel">
     <div class="offcanvas-header">
         <h5 class="offcanvas-title" id="offcanvasExampleLabel">{{ __('rank.edit') }} ({{ $rank->name }}) </h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
     </div>
     <div class="offcanvas-body">
         <div>
             <form action="{{ route('rank.update', ['rank' => $rank]) }}" method="post" class="mt-3"
                 onsubmit="updateRank(this)" enctype="multipart/form-data">
                 <noscript>
                     @method('patch')
                     @csrf
                 </noscript>
                 <div class="form-group">
                     <label>{{ __('rank.name') }}</label>
                     <input type="text" name="name" id="" class="form-control"
                         value="{{ $rank->name }}">
                 </div>
                 @if ($activeConfig->contains('slug', 'joiner-package'))
                     <div class="form-group">
                         <select class="form-control" name="package_id">
                             @foreach ($packages as $package)
                                 <option value="{{ $package->id ?? $package->product_id }}"
                                     @if ($package->id == $rank->package_id) selected @endif>
                                     {{ $package->name ?? $package->model }}</option>
                             @endforeach
                         </select>
                     </div>
                 @endif
                 @if ($activeConfig->contains('slug', 'referral-count'))
                     <div class="form-group">
                         <label>{{ __('rank.referral-count') }}</label>
                         <input type="number" name="referral_count" class="form-control" min="0"
                             value="{{ $rank->rankCriteria->referral_count ?? 0 }}">
                     </div>
                 @endif
                 @if ($activeConfig->contains('slug', 'personal-pv'))
                     <div class="form-group">
                         <label>{{ __('rank.personal-pv') }}</label>
                         <input type="number" name="personal_pv" class="form-control" min="0"
                             value="{{ $rank->rankCriteria->personal_pv ?? 0 }}">
                     </div>
                 @endif
                 @if ($activeConfig->contains('slug', 'group-pv'))
                     <div class="form-group">
                         <label>{{ __('rank.group-pv') }}</label>
                         <input type="number" name="group_pv" class="form-control" min="0"
                             value="{{ $rank->rankCriteria->group_pv ?? 0 }}">
                     </div>
                 @endif
                 @if ($moduleStatus->mlm_plan == 'Binary' || $moduleStatus->mlm_plan == 'Matrix')
                     @if ($activeConfig->contains('slug', 'downline-member-count'))
                         <div class="form-group">
                             <label>{{ __('rank.downline-member-count') }}</label>
                             <input type="number" name="downline_count" class="form-control" min="0"
                                 value="{{ $rank->rankCriteria->downline_count ?? 0 }}">
                         </div>
                     @endif
                     @if ($activeConfig->contains('slug', 'downline-package-count'))
                         @foreach ($packages as $k => $package)
                             @php
                                 $skipPck = 0;
                             @endphp
                             @forelse($rank->downinePackCount as $activePck)
                                 @if ($package->id == $activePck->id)
                                     @php
                                         $skipPck = $activePck->id;
                                     @endphp
                                     <div class="form-group">
                                         <label> {{ __('rank.minimum_count_of_downline_members_with_package') }}
                                             {{ $package->name }}</label>
                                         <input type="number" name="packageId[{{ $package->id }}]"
                                             class="form-control" min="0"
                                             value="{{ $activePck->id == $package->id ? $activePck->pivot->count : 0 }}">
                                     </div>
                                 @endif
                             @empty
                             @endforelse
                             @if ($package->id != $skipPck)
                                 <div class="form-group">
                                     <label>{{ __('rank.minimum_count_of_downline_members_with_package') }}
                                         {{ $package->name }}</label>
                                     <input type="number" name="packageId[{{ $package->id }}]" class="form-control"
                                         min="0" value="0">
                                 </div>
                             @endif
                         @endforeach
                     @endif
                     @if ($activeConfig->contains('slug', 'downline-rank-count'))
                         @foreach ($ranks as $key => $newRank)
                             @php
                                 $skipRnk = 0;
                             @endphp
                             @forelse($rank->downlineRankCount as $downlinernk)
                                 @if ($newRank->id == $downlinernk->id)
                                     @php
                                         $skipRnk = $downlinernk->id;
                                     @endphp
                                     <div class="form-group">
                                         <label>{{ __('rank.minimum_count_of_downline_members_with_rank') }}
                                             {{ $newRank->name }}</label>
                                         <input type="number" name="downlineRank[{{ $newRank->id }}]"
                                             class="form-control" min="0"
                                             value="{{ $newRank->id == $downlinernk->id ? $downlinernk->pivot->count : 0 }}">
                                     </div>
                                 @endif
                             @empty
                             @endforelse
                             @if ($newRank->id != $skipRnk)
                                 <div class="form-group">
                                     <label>{{ __('rank.minimum_count_of_downline_members_with_rank') }}
                                         {{ $newRank->name }}</label>
                                     <input type="number" name="downlineRank[{{ $newRank->id }}]"
                                         class="form-control" min="0" value="0">
                                 </div>
                             @endif
                         @endforeach
                     @endif
                 @endif
                 <div class="form-group">
                     <label>{{ __('rank.color') }}</label>
                     <input type="color" name="color" id="" class="form-control form-control-color"
                         value={{ $rank->color }}>
                 </div>
                 <div class="row">
                     <div class="form-group">
                         {{-- <div class="input-group"> --}}
                         <label>{{ __('rank.rank_achieve_bonus') }}</label>
                         <div class="input-group">
                             <span class="input-group-text"><i>{{ $currency }}</i></span>
                             <input type="number" name="commission" class="form-control"
                                 value="{{ formatCurrency($rank->commission) }}">
                         </div>
                     </div>
                 </div>
                 <div class="form-group">
                     <label for="">{{ __('rank.badge') }}</label>
                     <input type="file" name="image" id="image-select" onchange="getPrev(this)"
                         class="form-control">
                 </div>
                 <div class="form-group">
                    
                     @if (isFileExists($rank->image))
                         <img src="{{ $rank->image }}"
                             class="img-thumbnail mx-auto d-block rounded-circle avatar-sm rnk-img" width="85">
                         <i class="mdi mdi-delete btn btn-sm btn-danger icon-delete"
                             onclick="unlinkFile({{ $rank->id }})"></i>
                     @endif
                     <img src=""
                         class="img-thumbnail mx-auto d-block img-prev rounded-circle avatar-sm invisible"
                         width="85">

                 </div>
                 <div class="form-group">
                     <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                 </div>
             </form>
         </div>

     </div>
 </div>
