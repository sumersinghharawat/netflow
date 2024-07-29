@extends('layouts.app')
@section('title', 'Genealogy-Tree')

@section('content')
    <div class="row">
        <div class="col-12 genealogy_page_head_top">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('tree.geanolgy_tree') }}</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card search_box_tree_view">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="tree_view_btn_left_row">
                            <a class="btn tree_view_btn_left_1"
                                href="{{ route('network.downlineMembers') }}">{{ __('tree.downline_members') }}</a>
                            {{-- @if ($moduleStatus->mlm_plan != 'Unilevel' && config('mlm.demo_status') == 'yes')
                                <a class="btn tree_view_btn_left_1"
                                    href="{{ route('change.placement') }}">{{ __('tree.change_placement') }}</a>
                            @endif --}}
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <form action="{{ route('network.genealogy') }}">
                            <input type="hidden" name="search" value="true">
                            @if ($moduleStatus->mlm_plan == 'Unilevel')
                            <input type="hidden" name="isMore" value=$isMore  />
                            @endif
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
                                            href="{{ route('network.genealogy') }}">{{ __('common.reset') }} </a>
                                    </div>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="genealogy-body" style="overflow: visible;">
        <div class="tree_view_zoom_panel">
            <div class="tree_view_zoom_panel_btns" id="tree_view_zoomin"><i class='bx bx-zoom-in'></i></div>
            <div class="tree_view_zoom_panel_btns" id="tree_view_zoomout"><i class='bx bx-zoom-out'></i></div>
            <div class="tree_view_zoom_panel_btns" id="tree_view_reset"><i class='bx bx-sync'></i></div>
        </div>
        <div class="genealogy_tree_view_sec">
            <div class="genealogy-tree" id="tree-view-head"
                data-height="{{ $moduleStatus->mlm_plan == 'Monoline' ? '10px' : '240px' }}">
                {!! $renderTree !!}
            </div>
        </div>
    </div>

    <div class="toolTip-div">
        @forelse ($tooltipData as $item)
            <div data-serialtip-target="example-{{ $item->id }}" class="serialtip-default"
                data-username="{{ $item->username }}" id="ttip-{{ $item->id }}">
                <div class="tooltip-head">
                    @php
                        if ($treeIcon_based_on == 'member_pack') {
                            $image = $item->package->tree_icon ?? '/assets/images/users/avatar-1.jpg';
                        } elseif ($treeIcon_based_on == 'profile_image') {
                            $image = $item->userDetails->image ? $item->userDetails->image : '/assets/images/users/avatar-1.jpg';
                        } elseif ($treeIcon_based_on == 'rank') {
                            $image = $item->rankDetail->tree_icon ?? '/assets/images/users/avatar-1.jpg';
                        } elseif ($treeIcon_based_on == 'member_status') {
                            if ($item->active && $activeTreeIcon != '') {
                                $image = 'storage/' . $activeTreeIcon->image ?? '/assets/images/users/avatar-1.jpg';
                            } elseif ($item->active == 0 && $inActiveTreeIcon != '') {
                                $image = 'storage/' . $inActiveTreeIcon->image ?? '/assets/images/users/avatar-1.jpg';
                            } else {
                                $image = '/assets/images/users/avatar-1.jpg';
                            }
                        } else {
                            $image = '/assets/images/users/avatar-1.jpg';
                        }
                    @endphp

                    <div class="tooltip_image"><img src="{{ asset($image ?? '') }}" alt="" class="user-image"></div>
                    <span class="tootip-username">{{ $item->username }}</span>
                    @if ($tooltipConfig->contains('slug', 'first-name'))
                        <span class="tootltip-fullname">{{ $item->userDetails->name }}
                            {{ $item->userDetails->second_name }}</span>
                    @endif
                </div>
                <div class="tooltip-table">
                    <table>
                        @if ($tooltipConfig->contains('slug', 'join-date'))
                            <tr>
                                <td>{{ __('tree.join_date') }}</td>
                                <td>{{ $item->date_of_joining }}</td>
                            </tr>
                        @endif
                        @if ($moduleStatus->mlm_plan == 'Binary' && $tooltipConfig->contains('slug', 'left'))
                            <tr>
                                <td>{{ __('tree.left') }}</td>
                                <td> {{ $item->legDetails->total_left_count ?? 0 }}</td>
                            </tr>
                        @endif
                        @if ($moduleStatus->mlm_plan == 'Binary' && $tooltipConfig->contains('slug', 'right'))
                            <tr>
                                <td>{{ __('tree.right') }}</td>
                                <td>{{ $item->legDetails->total_right_count ?? 0 }}</td>
                            </tr>
                        @endif
                        @if ($moduleStatus->mlm_plan == 'Binary' && $tooltipConfig->contains('slug', 'left-carry'))
                            <tr>
                                <td>{{ __('tree.left_carry') }}</td>
                                <td>{{ $item->legDetails->total_left_carry ?? 0 }}</td>
                            </tr>
                        @endif
                        @if ($moduleStatus->mlm_plan == 'Binary' && $tooltipConfig->contains('slug', 'right-carry'))
                            <tr>
                                <td>{{ __('tree.right_carry') }}</td>
                                <td>{{ $item->legDetails->total_right_carry ?? 0 }}</td>
                            </tr>
                        @endif
                        @if ($tooltipConfig->contains('slug', 'personal-pv'))
                            <tr>
                                <td>{{ __('tree.personal_pv') }}</td>
                                <td>{{ $item->personal_pv }}</td>
                            </tr>
                        @endif
                        @if ($tooltipConfig->contains('slug', 'group-pv'))
                            <tr>
                                <td>{{ __('tree.group_pv') }}</td>
                                <td>{{ $item->gpv }}</td>
                            </tr>
                        @endif
                        @if (
                            $moduleStatus->mlm_plan == 'Donation' &&
                                $tooltipConfig->contains('slug', 'donation-level') &&
                                $item->donation_level)
                            <tr>
                                <td>{{ __('tree.donation_level') }}</td>
                                <td>{{ $item->donation_level }}</td>
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
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://www.jqueryscript.net/demo/rich-text-popover-serialtip/dist/jquery.serialtip.css" />
    <script src="https://www.jqueryscript.net/demo/rich-text-popover-serialtip/dist/jquery.serialtip.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>

    <script>
        $(() => {
            initTooltip();
            getUsers();
            const elem = document.getElementById('tree-view-head');
            const zoomIn = document.getElementById('tree_view_zoomin');
            const zoomOut = document.getElementById('tree_view_zoomout');
            const resetButton = document.getElementById('tree_view_reset');

            const panzoom = Panzoom(elem, {
                maxScale: 5
            });
            // panzoom.zoom(.7, { animate: true })


            const parent = elem.parentElement
            parent.addEventListener('wheel', function(event) {
                if (!event.shiftKey) return
                panzoom.zoomWithWheel(event)
            });
            zoomIn.addEventListener('click', panzoom.zoomIn);
            zoomOut.addEventListener('click', panzoom.zoomOut);
            // resetButton.addEventListener('click', panzoom.reset);

            let panTo = getNodeWidth(elem);
            resetButton.addEventListener('click', function() {
                panzoom.reset();
                if(panTo){
                    setTimeout(() => {
                        panTo = getNodeWidth(elem);
                        panzoom.pan(panTo + 650, 0, { animate: true});
                    }, 100);
                } else {
                    panzoom.reset;
                }
            });


            // Set the pan and scroll positions to center the target img element
            setTimeout(() => (panTo) ? panzoom.pan(panTo + 650, 0, { animate: true}) : '');
        });

        const getDownline = async (userId) => {
            let parentDiv = event.target.closest('div');
            let isNode = 0;
            let plan = "{{ $moduleStatus->mlm_plan }}";

            // if (plan == 'Monoline') {
            //     let downArrowImage = "#img-down-" + userId;
            //     $(downArrowImage).addClass('d-none')
            // }
            if (parentDiv.id == 'tree-view-head') {
                $('#expanded-tree-view').hide('slow').html('');
                isNode = 1;
            }

            let downArrowWidth = $(`#down-arrow-${userId}`).outerWidth();
            let node1 = $(`#down-arrow-${userId}`).offset().left + (downArrowWidth / 2);
            let level = event.target.dataset.level;
            let downlines = $('.downline-tree');

            if (downlines.length == 0) {
                if (parentDiv.id == `expanded-tree-0`) {
                    $('#expanded-tree-view').html('');
                    $('#tree-view-head').html('');
                    let parentDiv = element.dataset.parent;
                }
            }
            if (parentDiv.id == 'tree-view-head') {
                isNode = 1;
            }
            let data = event.target.dataset;
            let isMore = data.hasOwnProperty('ismore') == true;
            let isNew = true;
            const res = await $.get("{{ route('network.genealogy') }}", {
                user: userId,
                level: Number(level) + 1,
                isNode,
                isMore,
                isNew
            });
            $(`#node-id-${userId}`).slideUp(function() {
                $(this).html(res.data.tree).hide().slideDown(500, 'swing');
                $(`#ttip-${userId}`).remove();
                $('.toolTip-div').append(res.data.tooltip);
                initTooltip();
            });
        }

        const collapseTree = async (userId, element) => {
            let parentDiv = element.closest('div');
            let parentUl = element.closest('ul');
            let parentLevel = parentDiv.dataset.divLevel;
            const res = await $.get("{{ route('network.collapse.tree') }}", {
                user: userId,
            });
            $(`#node-id-${userId}`).slideUp(function() {
                $(this).html(res.data).hide().slideDown(500, 'swing');
                $('.toolTip-div').append(res.data.tootip);
                initTooltip();
            });
            // $(`#node-id-${userId}`).html(res.data).hide().slideDown(500, 'swing');
        }

        const getTree = async (userId) => {
            const res = await $.get("{{ route('network.genealogy') }}", {
                user: userId
            })
            if (typeof(res) != 'undefined') {
                console.log(res.data.tree);
                $('#tree-view-head').html(res.data.tree);
                $('.toolTip-div').append(res.data.tootip);
                initTooltip();
            }
        }
        const getsiblings = async (father, position) => {
            let targetEl = event.target;
            let isClicked = targetEl.dataset.clicked === 'true' ? true : false;
            if (isClicked) return true;

            let childCount = $(`li#node-id-${father} > ul > li[id^='node-id-']`).length;
            const fatherNode = $(`#node-id-${father}`);
            const node = fatherNode.find("ul:first");
            let url = `{{ route('network.more.child', ['father' => ':value', 'count' => ':count']) }}`;
            url = url.replace(':value', father);
            url = url.replace(':count', childCount);
            targetEl.dataset.clicked = 'true';

            const res = await $.get(`${url}`);
            if (res.status) {
                if (res.data.more) {
                    $(`#more-child-${father}`).html(`<strong>${res.data.more}</strong> More >`);
                    $(`#more-child-${father}`).data('data-clicked','false');
                } else {
                    $(`#more-children-li-${father}`).remove();
                }
                const lastChild = $(node).children("li[id^='node-id-']").last();

                let newLi = "";
                res.data.children.forEach(element => {
                    let liElement = document.createElement('li');
                    liElement.id  = `node-id-${element.user_id}`;
                    liElement.innerHTML = element.tree;
                    newLi += liElement.outerHTML;
                });
                // Animate the newly appended li elements with slideUp effect
                // lastChild.after(newLi).nextAll().hide().slideDown();
                // Animate the newly appended li elements with slideUp effect using GSAP
                lastChild.after(newLi);
                const newElements = lastChild.nextAll();
                newElements.css({ opacity: 0, transform: "translateX(-100%)" });

                TweenMax.staggerFromTo(newElements, 0.5, {
                    opacity: 0,
                    transform: "translateX(-100%)"
                }, {
                    opacity: 1,
                    transform: "translateX(0%)"
                }, 0.1);
                $('.toolTip-div').append(res.data.tooltipData);
                initTooltip();
            }

        }
        const getNodeWidth = (elem) => {
            const targetLi = $('#node-img-1'); // Change this to target a different li element.
            if(targetLi.length){
                const tarhetLiOffset = targetLi.offset().left;
                const targetImgWidth = targetLi.outerWidth();
                const treeOffset = $(elem).offset().left;
                const treeWidth = $(elem).outerWidth();
                // Calculate the x pan value to center the target img element
                const targetPanX = (tarhetLiOffset - treeOffset) - (targetImgWidth / 2);
                return - targetPanX;
            }
            return 0;
        }

        const searchUser = (user) => {
            location.href = `tree-genealogy?user=${user}`
        }
    </script>
@endpush
