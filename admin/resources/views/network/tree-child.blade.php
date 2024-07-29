<ul>
    @forelse ($data as $item)
        <li id="tree-child-{{ $item['id'] }}">
            <div class="col tree_view_card" >
                <div class="card">
                    <div class="row no-gutters align-items-center">
                        <div class="col-md-12">
                            <div class="tree_card_txt_sec">
                                <div class="tre_view_card_img">
                                    @if($item['image'])
                                        <img class="card-img img-fluid" src={{$item['image']}} alt="Card image">
                                    @else
                                        <img class="card-img img-fluid" src="/assets/images/users/avatar-1.jpg" alt="Card image">
                                    @endif
                                </div>
                                <h5 class="card-title">{{ $item['title'] }}
                                <div  class="tree_view_fll_name">{{ $item['full_name'] }}</div>
                                </h5>

                                <div class="tree_card_level">
                                Level
                                <span>{{ $item['level'] }}</span>
                                </div>
                                <div class="info_card_tree  tooltipstered" id="{{ $item['title'] }}-tooltip">
                                    <i class="fa fa-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @if ($item['child'])
            <a href="#" class="plus_btn expand" data-id="{{ $item['id'] }}" onclick="getChild({{ $item['id'] }})" id="child-{{ $item['id'] }}"><i id="icon-{{ $item['id'] }}" class="fas fa-plus-circle"></i></a>
            </div>
            @else
            </div>
            @endif
        </li>
    @empty

    @endforelse
</ul>
