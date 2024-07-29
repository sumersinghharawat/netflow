   <div id="extra">
       @isset($data['rank'])
           <div id="referral_rank_div">
               @foreach ($data['rank'] as $rank)
                   <div class="form-group">
                       <label class="required">
                           {{ __('compensation.referral_commission') }} - {{ $rank->name }}
                           <span
                               class="referral_commission percent_label">{{ ($data['config']->referral_commission_type == 'percentage') ? "%" : '' }}
                               </span>
                       </label>

                       <input type="text" maxlength="5"
                           class="level_percentage form-control @error('rank.' . $rank->id) is-invalid @enderror"
                           name="rank[{{ $rank->id }}]" min="0" value="{{ $rank->rankDetails->referral_commission }}">
                       @error('rank.' . $rank->id)
                           <span class="text-danger form-text">{{ $message }}</span>
                       @enderror
                   </div>
               @endforeach
           </div>
       @endisset
       @isset($data['level'])
           @foreach ($data['level'] as $item)
               <div class="form-group">
                   <label class="required">
                    {{ __('compensation.referral_commission') }} - {{ $item->name  ?? $item->model}}
                       <span class="span_referral_commission percent_label">
                           {{ ($data['config']->referral_commission_type == 'percentage') ? '%' : '' }}
                       </span>
                   </label>
                   <div class="input-group">
                        <span class="input-group-text">
                            <i>{{ $data['config']->referral_commission_type == 'percentage' ? '%' : $data['currency'] }}</i>
                        </span>
                        <input type="text" maxlength="5"
                            class="level_percentage form-control  @error('product.' . $item->id) is-invalid @enderror"
                            name="product[{{ $item->id ?? $item->product_id }}]" min="0"
                            @if($data['config']->referral_commission_type == 'percentage')
                                value="{{ $item->referral_commission }}"
                            @else value="{{ formatCurrency($item->referral_commission) }}" @endif
                                >
                        @error('product.' . $item->id)
                            <span class="text-danger form-text">{{ $message }}</span>
                        @enderror
                   </div>

               </div>
           @endforeach
       @endisset
   </div>
