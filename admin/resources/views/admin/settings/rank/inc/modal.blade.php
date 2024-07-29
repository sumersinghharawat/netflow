 <div class="offcanvas offcanvas-end" tabindex="-1" id="addNewRank" aria-labelledby="offcanvasExampleLabel">
     <div class="offcanvas-header">
         <h5 class="offcanvas-title" id="offcanvasExampleLabel">{{ __('rank.add') }}</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
     </div>
     <div class="offcanvas-body">
         <div>
             <form action="{{ route('rank.store') }}" method="post" class="mt-3" onsubmit="createRank(this)"
                 enctype="multipart/form-data">
                 <noscript>
                     @csrf
                 </noscript>
                 <div class="form-group">
                     <label>{{ __('rank.name') }}</label>
                     <input type="text" name="name" id="" class="form-control">
                 </div>
                 @if ($activeConfig->contains('slug', 'joiner-package'))
                     <div class="form-group">
                         <select class="form-control" name="package_id">
                             @foreach ($packages as $package)
                                 <option value="{{ $package->id ?? $package->product_id }}">{{ $package->name ?? $package->model}}</option>
                             @endforeach
                         </select>
                     </div>
                 @endif
                 @if ($activeConfig->contains('slug', 'referral-count'))
                     <div class="form-group">
                         <label>{{ __('rank.referral-count') }}</label>
                         <input type="number" name="referral_count" class="form-control" min="0">
                     </div>
                 @endif
                 @if ($activeConfig->contains('slug', 'personal-pv'))
                     <div class="form-group">
                         <label>{{ __('rank.personal-pv') }}</label>
                         <input type="number" name="personal_pv" class="form-control" min="0">
                     </div>
                 @endif
                 @if ($activeConfig->contains('slug', 'group-pv'))
                     <div class="form-group">
                         <label>{{ __('rank.group-pv') }}</label>
                         <input type="number" name="group_pv" class="form-control" min="0">
                     </div>
                 @endif
                 @if ($moduleStatus->mlm_plan == 'Binary' || $moduleStatus->mlm_plan == 'Matrix')
                     @if ($activeConfig->contains('slug', 'downline-member-count'))
                         <div class="form-group">
                             <label>{{ __('rank.downline-member-count') }}</label>
                             <input type="number" name="downline_count" class="form-control" min="0">
                         </div>
                     @endif
                     @if ($activeConfig->contains('slug', 'downline-package-count'))
                         @foreach ($packages as $package)
                             <div class="form-group">
                                 <label>{{ __('rank.minimum_count_of_downline_members_with_package') }} -
                                     {{ $package->name }}</label>
                                 <input type="number" name="packageId[{{ $package->id }}]" class="form-control"
                                     min="0">
                             </div>
                         @endforeach
                     @endif
                     @if ($activeConfig->contains('slug', 'downline-rank-count'))
                         @foreach ($ranks as $rank)
                             <div class="form-group">
                                 <label>{{ __('rank.minimum_count_of_downline_members_with_rank') }} -
                                     {{ $rank->name }}</label>
                                 <input type="number" name="downlineRank[{{ $rank->id }}]" class="form-control"
                                     min="0">
                             </div>
                         @endforeach
                     @endif
                 @endif

                 <div class="form-group">
                     <label>{{ __('rank.color') }}</label>
                     <input type="color" name="color" id="" class="form-control form-control-color">
                 </div>
                 <div class="form-group">
                     <label>{{ __('rank.rank_achieve_bonus') }}</label>
                     <input type="number" name="commission" class="form-control">
                 </div>
                 <div class="form-group">
                     <label>{{ __('rank.rank_priority') }}</label>
                     <input type="number" name="priority" class="form-control">
                 </div>
                 <div class="form-group">
                     <label for="">{{ __('rank.badge') }}</label>
                     <input type="file" name="image" id="image-select-1" onchange="getPrev(this)"
                         class="form-control">
                 </div>
                 <div class="form-group">
                     <img src="" class="img-thumbnail mx-auto d-block invisible img-prev" width="85"
                         id="img-prev">
                 </div>
                 <div class="form-group">
                     <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
                 </div>
             </form>
         </div>

     </div>
 </div>
