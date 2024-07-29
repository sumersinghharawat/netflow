@extends('layouts.app')
@section('title', 'Manage Module Status')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4>Manage Module Status <h4>
            </div>
        </div>
    </div><br>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body" id="epinTable">
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap table-check" id="epinlist">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-middle">Module Name</th>
                                    <th class="align-middle">Status</th>
                                    <th class="align-middle">Configuration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        E-pin
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="epin" switch="none"
                                                @checked($moduleStatus->pin_status) onchange="updateModule(this)"
                                                value="pin_status" name="pin_status">
                                            <label for="epin" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    @if ($moduleStatus->pin_status)
                                        <td>
                                            <a href="{{ route('epin.index') }}" class="plan_commission"
                                                title="click to check config"><i class="fa fa-cog"
                                                    aria-hidden="true"></i></a>
                                        </td>
                                    @endif
                                </tr>

                                <tr>
                                    <td>
                                        Package
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="package" switch="none"
                                                @checked($moduleStatus->product_status) name=product_status onchange="updateModule(this)">
                                            <label for="package" data-on-label="On" data-off-label="Off"
                                                ></label>
                                        </div>
                                    </td>
                                    @if ($moduleStatus->product_status)
                                        <td>
                                            <a href="{{ route('package') }}" class="plan_commission"
                                                title="click to check config"><i class="fa fa-cog"
                                                    aria-hidden="true"></i></a>
                                        </td>
                                    @endif
                                </tr>

                                <tr>
                                    <td>
                                        Rank
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="rank" switch="none"
                                                @checked($moduleStatus->rank_status) name="rank_status"
                                                onchange="updateModule(this)">
                                            <label for="rank" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    @if ($moduleStatus->rank_status)
                                        <td>
                                            <a href="{{ route('rank') }}" class="plan_commission"
                                                title="click to check config"><i class="fa fa-cog"
                                                    aria-hidden="true"></i></a>
                                        </td>
                                    @endif
                                </tr>

                                <tr>
                                    <td>
                                        Multi Language
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="lang" switch="none"
                                                @checked($moduleStatus->multilang_status) name="multilang_status"
                                                onchange="updateModule(this)">
                                            <label for="lang" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    @if ($moduleStatus->multilang_status)
                                        <td>
                                            <a href="{{ route('language') }}" class="plan_commission"
                                                title="click to check config"><i class="fa fa-cog"
                                                    aria-hidden="true"></i></a>
                                        </td>
                                    @endif
                                </tr>

                                <tr>
                                    <td>
                                        Multi Currency
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="currency" switch="none"
                                                @checked($moduleStatus->multi_currency_status) name="multi_currency_status"
                                                onchange="updateModule(this)">
                                            <label for="currency" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    @if ($moduleStatus->multi_currency_status)
                                        <td>
                                            <a href="{{ route('currency') }}" class="plan_commission"
                                                title="click to check config"><i class="fa fa-cog"
                                                    aria-hidden="true"></i></a>
                                        </td>
                                    @endif
                                </tr>

                                <tr>
                                    <td>
                                        Lead Capture Status
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="lcp" switch="none"
                                                @checked($moduleStatus->lead_capture_status) name="lead_capture_status"
                                                onchange="updateModule(this)">
                                            <label for="lcp" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    <td></td>


                                </tr>

                                <tr>
                                    <td>
                                        Ticket System Status
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="ticket" switch="none"
                                                @checked($moduleStatus->ticket_system_status) name="ticket_system_status"
                                                onchange="updateModule(this)">
                                            <label for="ticket" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    <td></td>

                                </tr>

                                <tr>
                                    <td>
                                        Auto Responder Status
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="auto_responder" switch="none"
                                                @checked($moduleStatus->autoresponder_status) name="autoresponder_status"
                                                onchange="updateModule(this)">
                                            <label for="auto_responder" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="{{ route('responder.index') }}" class="plan_commission"
                                            title="click to check config"><i class="fa fa-cog"
                                                aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Replicated Site Status
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="replica" switch="none"
                                                @checked($moduleStatus->replicated_site_status) name="replicated_site_status"
                                                onchange="updateModule(this)">
                                            <label for="replica" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    <td></td>

                                </tr>
                                <tr>
                                    <td>
                                        Privileged User
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="employee" switch="none"
                                                @checked($moduleStatus->employee_status) name="employee_status"
                                                onchange="updateModule(this)">
                                            <label for="employee" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    @if ($moduleStatus->employee_status)
                                        <td>
                                            <a href="{{ route('privileged-user.index') }}" class="plan_commission"
                                                title="click to check config"><i class="fa fa-cog"
                                                    aria-hidden="true"></i></a>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>
                                        SMS
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="sms" switch="none"
                                                @checked($moduleStatus->sms_status) name="sms_status"
                                                onchange="updateModule(this)">
                                            <label for="sms" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    {{-- <td>
                                        <a href="#" class="plan_commission" title="click to check config"><i
                                                class="fa fa-cog" aria-hidden="true"></i></a>
                                    </td> --}}
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        Live Chat
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="live_chat" switch="none"
                                                @checked($moduleStatus->live_chat_status) name="live_chat_status"
                                                onchange="updateModule(this)">
                                            <label for="live_chat" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    <td></td>

                                </tr>

                                <tr>
                                    <td>
                                        Help
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="help" switch="none"
                                                @checked($moduleStatus->help_status) name="help_status"
                                                onchange="updateModule(this)">
                                            <label for="help" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    <td></td>


                                </tr>
                                <tr>
                                    <td>
                                        Purchase Status
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="purchase_status" switch="none"
                                                @checked($moduleStatus->repurchase_status) name="repurchase_status"
                                                onchange="updateModule(this)">
                                            <label for="purchase_status" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    <td></td>

                                </tr>
                                <tr>
                                    <td>
                                        Package Upgrade
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="upgrade" switch="none"
                                                @checked($moduleStatus->package_upgrade) name="package_upgrade"
                                                onchange="updateModule(this)">
                                            <label for="upgrade" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    <td></td>

                                </tr>
                                <tr>
                                    <td>
                                        HYIP/ROI
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="hype" switch="none"
                                                @checked($moduleStatus->hyip_status) name="hyip_status"
                                                onchange="updateModule(this)">
                                            <label for="hype" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    <td></td>


                                </tr>
                                <tr>
                                    <td>
                                        X-Up Commission
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="xup" switch="none"
                                                @checked($moduleStatus->xup_status) name="xup_status"
                                                onchange="updateModule(this)">
                                            <label for="xup" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    <td></td>


                                </tr>

                                <tr>
                                    <td>
                                        KYC
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="kyc" switch="none"
                                                @checked($moduleStatus->kyc_status) name="kyc_status"
                                                onchange="updateModule(this)">
                                            <label for="kyc" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>
                                    <td></td>

                                </tr>

                                <tr>
                                    <td>
                                        Mailgun status
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="mailgun" switch="none"
                                                @checked($moduleStatus->mail_gun_status) name="mail_gun_status"
                                                onchange="updateModule(this)">
                                            <label for="mailgun" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    <td></td>

                                </tr>

                                <tr>
                                    <td>
                                        Signup Configuration
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="signup" switch="none"
                                                @checked($moduleStatus->signup_config) name="signup_config"
                                                onchange="updateModule(this)">
                                            <label for="signup" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    <td></td>

                                </tr>
                                <tr>
                                    <td>
                                        Purchase Wallet
                                    </td>

                                    <td>
                                        <div class="square-switch">
                                            <input type="checkbox" id="purchase" switch="none"
                                                @checked($moduleStatus->purchase_wallet) name="purchase_wallet"
                                                onchange="updateModule(this)">
                                            <label for="purchase" data-on-label="On" data-off-label="Off"></label>
                                        </div>
                                    </td>

                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const updateModule = async (input) => {

            try {
                let isChecked = (input.checked == true ? 1 : 0);
                let field = input.name;
                let url = '/update-modules';
                let data = {
                    field: field,
                    status: isChecked
                }
                const res = await $.post(`${url}`, data);

                location.reload();

            } catch (error) {
                console.log(error);
            }
        }
    </script>
@endpush
