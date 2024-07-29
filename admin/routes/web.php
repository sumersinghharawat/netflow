<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'Localization', 'Permission'])->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::get('/top/recruiters', 'getTopRecruiters')->name('dashboard.getTopRecruiters');
        Route::get('/package/progress', 'getPackageProgressData')->name('dashboard.PackageProgress');
        Route::get('/ranks', 'getRankData')->name('dashboard.rankData');
        Route::get('/income-bonus-graph', 'getIncomeBonusGraph')->name('income.bonus.graph');
        Route::get('/get-joinings-graph', 'getJoiningsGraph')->name('joinings.graph');

        Route::get('/get-joinings-graph-L', 'getLeftJoiningsGraph')->name('joinings.graph.binary.left');
        Route::get('/get-joinings-graph-R', 'getRightJoiningsGraph')->name('joinings.graph.binary.right');

        Route::get('/get-top-earners', 'getTopEarners')->name('top-earners');
        Route::get('/get-income-commission', 'getIncomeCommission')->name('income.commission');
    });

    Route::prefix('admin')->group(function () {
        Route::controller(ApprovalController::class)->group(function () {
            Route::get('/registration/pending', 'index')->name('approval.index');
            Route::post('/registration/approve', 'approve')->name('approval.approve');
        });

        Route::controller(CommonMailSettingController::class)->group(function () {
            Route::get('/mail-content', 'index')->name('mailcontent');
            Route::get('/mail-content-edit/{id}/{language_id}', 'edit')->name('mailcontent-edit');
            Route::post('/mail-content-update/{id}', 'update')->name('mailcontent-update');
            Route::post('/mail-content-addnew', 'addnew')->name('mailcontent-addnew');
        });
        Route::controller(CompanyProfileController::class)->group(function () {
            Route::get('/site/information', 'index')->name('company.profile');
            Route::post('/site/information/{id?}', 'update')->name('companyProfile.update');
            Route::post('/site/logos/{id?}', 'updateLogo')->name('companyLogo.update');
            Route::get('/set-theme', 'setTheme');
            Route::post('/site/deleteImg', 'deleteImage')->name('companyProfile.deleteImage');
        });

        Route::controller(ToolsController::class)->group(function () {
            Route::get('/leads/{type?}', 'viewLead')->name('leads');
            //Route::get('/filter/view-lead', 'filterLead')->name('crm.filter.view-lead');
            Route::get('/view/leads/{data?}', 'getViewLeads')->name('get.leads');
        });

        Route::controller(CurrencyController::class)->group(function () {
            Route::get('/currency', 'index')->name('currency');
            Route::get('/currency_edit/{id?}', 'currencyEdit')->name('currencyEdit');
            Route::post('currency_update/{id}', 'currencyUpdate')->name('currencyUpdate');
            Route::get('/currencyView/{id?}', 'currencyView')->name('currencyView');
            Route::post('/currency_dis/{id}', 'currencyStatusChange')->name('currencyDis');
            Route::patch('/currency/set-default/{currency}', 'setDefault')->name('set.default.currency');
            Route::get('/currency-change', 'changeCurrency')->name('change.currency');
        });

        Route::controller(MatchingBonusController::class)->group(function () {
            Route::get('/matchingbonus', 'index')->name('matching_bonus');
            Route::post('/matching-bonus/update', 'update')->name('matchingbonus.config.update');
            Route::post('/matching-bonus/genealogy/update', 'commissionUpdate')->name('matching.bonus.commission.update');
        });

        Route::controller(PackageController::class)->group(function () {
            Route::get('/membership/package', 'index')->name('package');
            Route::put('/edit/member/package/{id?}', 'packageEdit')->name('member.package.edit');
            Route::post('/update/package/{id?}', 'packageUpdate')->name('member.package.update');
            Route::post('/packageDis/{id?}', 'packageStatusChange')->name('packageDis');
            Route::post('/store/membership/package/{id?}', 'store')->name('member.package.store');
            Route::post('/member/package/filter', 'filter')->name('member.package.filter');
            Route::post('/package/create-payment-id', 'createPaymentId')->name('package.create-payment-id');
            Route::post('/store/membership/new-package/{id?}', 'storeNewPackage')->name('member.newpackage.store');
        });

        Route::controller(RepurchaseController::class)->group(function () {
            Route::get('/package/repurchase', 'index')->name('package.repurchase');
            Route::put('/rePurchaseEdit/{id}', 'edit')->name('package.repurchase.edit');
        });
        Route::controller(RepurchaseCategoryController::class)->group(function () {
            Route::post('/category/store/{id?}', 'store')->name('repurchase.category.store');
            Route::put('/edit/repurchase/category/{id?}', 'edit')->name('repurchase.category.edit');
            Route::post('/repurchase/category/status/{id?}', 'status')->name('repurchase.status');
            Route::post('/repurchase/filter', 'filter')->name('repurchase.filter');
            Route::get('/repurchase/categories', 'getCategories')->name('repurchase.getCategories');
        });

        Route::controller(PerformanceBonusController::class)->group(function () {
            Route::get('/performance_bonus', 'index')->name('performance_bonus');
            Route::post('/performance_bonus_update/{id}', 'update')->name('performance_bonus_update');
        });

        Route::controller(RankController::class)->group(function () {
            Route::get('/rank-configuration', 'index')->name('rank');
            Route::put('/rank-configuration', 'rankConfigUpdate')->name('rank.updateConfig');
            Route::post('/add/new/rank', 'store')->name('rank.store');
            Route::put('/edit/rank/{id?}', 'rankEdit')->name('rank.edit');
            Route::post('update/rank/{rank}', 'rankUpdate')->name('rank.update');
            Route::patch('/rank/status/update/{id?}', 'rankStatusUpdate')->name('rankStatus.update');
            Route::delete('/rank-unlink-file/{rank}', 'unlinkImage')->name('rank.unlink.file');
        });

        Route::controller(SignupFieldController::class)->group(function () {
            Route::get('custom-fields', 'index')->name('signupField');
            Route::post('/custom-fields', 'store')->name('signupField.store');
            Route::post('/custom-fields/{id?}', 'destroy')->name('signupField.destroy');
            Route::post('/update-signup-fields', 'signupFieldUpdate')->name('signupField.update');
            Route::post('/update-custom-fields/{id?}', 'newcustomFieldUpdate')->name('customField.update');
            Route::get('/edit-custom-fields/{id?}', 'edit')->name('edit.customfield');
        });

        Route::controller(PoolBonusController::class)->group(function () {
            Route::get('pool_bonus', 'poolbonus')->name('poolbonus');
            Route::post('/pool_bonus', 'poolbonusUpdate')->name('poolbonus.update');
        });
        Route::controller(FastStartBonusController::class)->group(function () {
            Route::get('fast_start_bonus', 'faststartbonus')->name('faststartbonus');
            Route::post('/fast_start_bonus/{id?}', 'faststartbonusUpdate')->name('faststartbonus.update');
        });

        Route::controller(ReferralCommissionController::class)->group(function () {
            Route::get('commissions/referral', 'referralCommission')->name('referralcommission');
            Route::post('/referral_commissions', 'referralCommissionUpdate')->name('referralcommission.update');
        });

        Route::controller(RepurchaseSalesCommissionController::class)->group(function () {
            Route::get('/configuration/commission/sales', 'index')->name('salescommission');
            Route::put('/configuration/{id?}', 'updateConfig')->name('update.salesConfig');
            Route::put('/sales/geneology', 'geneologyUpdate')->name('sales.update.geneology');
            Route::put('/sales/rank/update', 'saleRankUpdate')->name('sales.update.rank');
        });

        Route::controller(PaymentController::class)->group(function () {
            Route::get('/view/payment-methods', 'index')->name('payment.view');
            Route::post('/update/bank-info/{id?}', 'bankDetailUpdate')->name('bankdetail.update');
            Route::post('payment-methods', 'update')->name('payment.update');
            Route::get('get-stripe-key', 'getStripeKey')->name('get.stripe.key');
        });

        Route::controller(SettingsController::class)->group(function () {
            Route::get('/commission/settings', 'commissionSettings')->name('commission');
            Route::get('/signup/settings', 'signupSettings')->name('signup');
            Route::post('/update-signup-settings', 'update_signupSettings')->name('signup.settings.update');
            Route::post('/commission', 'commissionUpdate')->name('commission.update');
            Route::get('/settings/compensation', 'compensationSettings')->name('compensation');

            Route::post('/compensation', 'compensationUpdate')->name('compensation.update');
            Route::post('/update-compensation/{id?}', 'singlecompensationUpdate')->name('compensation.updateSingle');
            Route::get('/binary-bonus', 'binaryConfig')->name('binaryConfig');
            Route::post('/binary-bonus-config', 'binaryConfigUpdate')->name('binaryConfig.update');
            Route::post('level_commissions_view/{id?}', 'levelCommissionViewUpdate')->name('levelcommissionview.update');
            Route::get('configuration-roicommission', 'Roicommission')->name('roicommission');
            Route::post('roicommission-update', 'updateRoicommission')->name('roiCommission.update');
            Route::get('/configuration/mail', 'viewMail')->name('mail');
            Route::post('update-mailSettings', 'updateMailSettings')->name('update.mailSettings');
            Route::get('api-key', 'getApiKey')->name('apiKey');
            Route::post('update-apiKey', 'updateApiKey')->name('update.apiKey');
            Route::post('generate-api', 'generateApi')->name('generate.Api');
            Route::get('session-time', 'getDynamicSessionoutTime')->name('sessionTime');
            Route::get('/user/activities', 'getUserActivity')->name('user.activity');
        });

        Route::controller(LevelCommissionController::class)->group(function () {
            Route::get('/commission/level', 'index')->name('levelcommission');
            Route::post('/config/level', 'configUpdate')->name('levelcommission.updateConfig');
            Route::post('/level/geneology', 'geneologyUpdate')->name('levelcommission.geneologyUpdate');
            Route::post('/commissions/level', 'update')->name('levelcommission.update');
            Route::patch('/commission/donation/update', 'donationLevelUpdate')->name('donationLevel.update');
        });

        Route::controller(ContentManagementController::class)->group(function () {
            Route::get('/welcome-letter', 'index')->name('welcome.letter');
            Route::post('/welcome-letter', 'updateWelcomeLetter')->name('welcome-letter.update');
            Route::post('/terms-and-conditions', 'updateTerms')->name('termsconditions.update');
            Route::get('/replication-site', 'replicaSite')->name('replication.site');
            Route::get('/replication-site-edit/{id}', 'replicaSiteEdit')->name('replication.site.edit');
            Route::post('replication-site-update-default/{id?}', 'replicaSiteUpdateDefault')->name('replication.site.update.default');
            Route::post('replication-site-update', 'replicaSiteUpdate')->name('replication.site.update');
            Route::post('bannerdefault-update/{id?}', 'updateBannerDefault')->name('bannerdefault.update');
            Route::post('banner-update/{id?}', 'updateBanner')->name('banner.update');
            Route::post('banner-create', 'createBanner')->name('banner.create');
            Route::post('replication-site-add-default/{id?}', 'replicaSiteStore')->name('replication.site.add.default');
        });

        Route::controller(UserDashboardController::class)->group(function () {
            Route::get('/view-dashboard-items', 'index')->name('userdashboard.view');
            Route::put('/dashboard-items', 'update')->name('userdashboard.update');
        });

        Route::controller(DocumentController::class)->group(function () {
            Route::get('/material', 'index')->name('material');
            Route::get('/material-add', 'add')->name('material.add');
            Route::post('/material-addnew', 'addnew')->name('material.addnew');
            Route::post('/material-delete/{id}', 'delete')->name('material.delete');
        });
        Route::controller(EwalletController::class)->group(function () {
            Route::get('/ewallet', 'index')->name('ewallet');
            Route::get('ewallet-transaction', 'ewalletTransaction')->name('ewallet.transaction');
            Route::get('ewallet-statement', 'ewalletStatement')->name('ewallet.statement');
            Route::get('ewallet-balance', 'ewalletBalance')->name('ewallet.balance');
            Route::get('purchase-wallet', 'purchaseWallet')->name('ewallet.purchase');
            Route::get('user-earnings', 'userEarnings')->name('user.earnings');
            Route::get('all-users', 'Alluser')->name('load.allusers');
            Route::get('ewalletsummary-report', 'ewalletSummaryReport')->name('ewallet.dateReport');
            Route::post('ewallet-fund-transfer', 'fundTransfer')->name('ewallet.fund.transfer');
            Route::post('ewallet-fund-credit', 'fundCredit')->name('ewallet.fund.credit');
            Route::post('ewallet-fund-debit', 'fundDebit')->name('ewallet.fund.debit');
            Route::get('show/ewallet-balance/{id}', 'showEwalletBalance')->name('show.ewallet-balance');
            Route::post('/cart/checkavailability', 'checkEwalletAvailability')->name('cart.check.ewallet');
            Route::post('/cart/purWalletAvailability', 'checkPurchaseWalletAvailability')->name('cart.check.pwallet');
        });

        Route::controller(PackageUpgradeController::class)->group(function () {
            Route::get('/package/upgrade/{id?}', 'index')->name('package.upgrade');
            Route::get('/package/upgrade/search/filter', 'filter')->name('package-upgrade.filter');
            Route::post('/package/upgrade/payment/{id?}', 'payment')->name('package.upgrade.payment');
            Route::post('/package/upgrade/submit', 'packageSubmit')->name('package.upgrade.submit');
            Route::post('/upgrade/add/payment/receipt', 'upgradeAddPaymentReceipt')->name('upgrade.add-payment-receipt');
        });

        Route::controller(TreeSettingsController::class)->group(function () {
            Route::get('/view-tree', 'index')->name('tree.view');
            Route::post('tooltip-details/update', 'update')->name('tooltip.update');
            Route::post('/view-tree/update', 'update')->name('tooltipconfig.update');
            Route::post('membership-package/image-store/{id}', 'MembershipPackImage')->name('membership-package-image.store');
            Route::post('rankdetails/update/{id}', 'update_rank')->name('rank.details.update');
            Route::get('update/configuration', 'update_config')->name('update.config');
            Route::put('/tree-size', 'updateTreeSize')->name('tree.size.upate');
        });

        Route::controller(EpinConfigController::class)->group(function () {
            Route::get('/config/pin', 'index')->name('pinconfig.index');
            Route::post('/config/{id?}', 'update')->name('pinconfig.update');
            Route::post('/create/number', 'pinNumberAdd')->name('pinNumber.store');
            Route::post('/delete/number', 'pinNumberDelete')->name('pinNumber.destroy');
        });
        Route::controller(EpinController::class)->group(function () {
            Route::get('/epin', 'index')->name('epin.index');
            Route::post('/epin', 'store')->name('epin.store');
            Route::post('/epin/purchase', 'purchaseStore')->name('epinPurchase.store');
            Route::post('/epin/transfer', 'epinTransfer')->name('epin.transfer');
            Route::post('/epin/user/', 'getUserEpinList')->name('epin.userlist');
            Route::post('/epin/delete/{id?}', 'delete')->name('epin.delete');
            Route::post('/requested/epin/{id?}', 'deleteRequestedEpin')->name('requestedEpin.delete');
            Route::post('/filter/pending/requests', 'filterPendingRequest')->name('specified.users');
            Route::post('/allocate/pin/{id?}', 'allocatePendingEpins')->name('allocate.epin');
            Route::post('/epin/status/{id?}', 'statusChange')->name('status.epin');
            Route::get('/get/epins', 'getEpins')->name('get.epins');
            Route::get('/filter/epins', 'filter')->name('epin.filter');
            Route::get('/active/pins', 'activeEpins')->name('get.active.epins');
            Route::get('/request/epins', 'PendingRequests')->name('get.requested.epins');
            Route::post('/cart/epin/availability', 'checkEpinAvailabilityForCart')->name('cart.check.epin');
        });

        Route::controller(FaqController::class)->group(function () {
            Route::get('/faq', 'index')->name('faq');
            Route::post('faq-create', 'create')->name('faq.create');
            Route::get('faq-delete/{id}', 'delete')->name('faq.delete');
            Route::post('check/sort-order/', 'checkSortOrder')->name('faq.sortOrder');
        });
        Route::controller(ImageUploadController::class)->group(function () {
            Route::post('image', 'store')->name('image.store');
            Route::delete('image', 'destroy')->name('image.delete');
        });

        Route::controller(MailBoxController::class)->group(function () {
            Route::get('/mailBox', 'viewmailbox')->name('mailBox');
            Route::get('/autoResponder', 'viewAutoResponder')->name('autoResponder');
            Route::get('/readMail/{id}/{type?}/{page?}', 'readSingleMail')->name('read.mail');
            Route::get('compose-mail', 'compose')->name('mail.compose');
            Route::post('compose-mail', 'storeCompose')->name('store.composemail');
            Route::get('sent-mail', 'sent')->name('mail.sent');
            Route::post('delete-mail/{id?}/{type?}', 'delete')->name('mail.delete');
            Route::get('replyMail/{id}', 'replyMail')->name('replyMail');
            Route::post('send-test-mail', 'sendTest')->name('test.mail');
        });

        Route::controller(NewsController::class)->group(function () {
            Route::get('/news', 'index')->name('news');
            Route::get('news/add', 'add')->name('news.add');
            Route::post('news/add-news', 'addNews')->name('news.addnews');
            Route::get('news/edit/{id}', 'edit')->name('news.edit');
            Route::post('/news/update/{id}', 'update')->name('news.update');
            Route::post('news/delete/{id}', 'delete')->name('news.delete');
        });

        Route::controller(PayoutController::class)->group(function () {
            Route::get('/payout-settings', 'index')->name('payout');
            Route::post('/update-payout', 'updatePayout')->name('payout.update');
            Route::post('/payment-method-update', 'paymentMethodUpdate')->name('paymentmethod.update');
            Route::get('kyc-category', 'kycCategory')->name('kyc_category');
            Route::post('/kyc-category-add', 'kycCategoryAdd')->name('kyc.category.add');
            Route::get('/kyc-category-edit/{id}', 'kycCategoryEdit')->name('kyc_category_edit');
            Route::post('/kyc-category-update/{id}', 'kycCategoryUpdate')->name('kyc_category_update');
            Route::delete('/kyc-category-delete/{id}', 'kycCategoryDelete')->name('kyc.category.delete');
        });
        Route::controller(SMSSettingController::class)->group(function () {
            Route::get('/sms-content', 'index')->name('smscontent');
            Route::post('/sms-content/store', 'store')->name('smscontent.add');
            Route::put('/smscontent/update', 'update')->name('smscontent.update');
            Route::get('get-smsContent', 'get_content')->name('get.sms.content');
            Route::post('smscontent/delete/{id}', 'delete')->name('smscontent.delete');
        });
        Route::controller(SMSTypeController::class)->group(function () {
            Route::get('/sms-type', 'index')->name('smstype');
            Route::post('/sms-type/delete/{id}', 'delete')->name('smstype.delete');
            Route::post('/sms-type/create', 'store')->name('smstype.add');
            Route::put('/sms-type-update', 'update')->name('smstype.update');
            Route::get('/get-sms-type', 'get_type')->name('get.sms.type');
            Route::get('sms-tpe/toggle/{id}', 'toggle')->name('toggle.smstype');
        });

        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile-settings', 'index')->name('profile');
            Route::post('/profile-settings/update', 'update')->name('profile_update');
        });
        Route::controller(ProfileDetailsController::class)->group(function () {
            Route::get('/profile/view', 'index')->name('profile.view');
            Route::post('/profile/{id?}', 'profileUpdate')->name('profile.update');
            Route::post('trans-password', 'transPasswordUpdate')->name('trans-password.update');
            Route::post('password-update', 'passwordUpdate')->name('password.update');
            Route::post('/profile-details/{id?}', 'profileDetailsUpdate')->name('profileDetail.update');
            Route::post('/bank-details/{id?}', 'bankDetailsUpdate')->name('bankDetail.update');
            Route::post('/stripe-details/{id?}', 'stripeDetailsUpdate')->name('stripeDetail.update');
            Route::post('/nowpayment-details/{id?}', 'nowpaymentDetailsUpdate')->name('nowpaymentDetail.update');
            Route::post('/paypal-details', 'paypalDetailsUpdate')->name('paypalDetails.update');
            Route::post('/contact-details/{id?}', 'contactDetailsUpdate')->name('contactDetail.update');
            Route::get('search-user', 'searchUser')->name('search.user');
            Route::post('/member-change-status', 'ChangeUserActiveStatus')->name('change.user.active.status');
            Route::post('/update/payment/details/{userid}', 'updatePaymentDetails')->name('update.user.payment-details');
            Route::post('/update/settings/{userid}', 'updateDefaultSettings')->name('update.default.settings');
            Route::post('/additional-details', 'additionalDetailUpdate')->name('additionalDetail.update');
            Route::post('/update/userpv/{userid?}', 'updatePv')->name('update.user.pv');
        });

        Route::controller(Report\ProfileController::class)->group(function () {
            Route::get('/report/profile', 'profileView')->name('reports.profile');
            Route::get('load-users', 'getUsers')->name('load.users');
            Route::post('profile-report', 'userReport')->name('users.report');
            Route::get('user-profile-report', 'userDateReport')->name('users.dateReport');
            Route::get('validate-user', 'validate_user')->name('validate.user');
        });
        Route::controller(SubscriptionManagementController::class)->group(function () {
            Route::get('/subscription', 'index')->name('subscription');
            Route::post('subscription/{id?}', 'update')->name('subscription.update');
            Route::post('package/{id?}', 'updateSubscriptionPeriod')->name('subscriptionPackage.update');
        });

        Route::controller(TreeController::class)->group(function () {
            Route::get('/tree-view', 'treeView')->name('network.treeview');
            Route::get('/tree/get-tree-view', 'getTreeView')->name('tree.view.ajax');
            Route::get('/tree/get-child', 'getChild')->name('tree.get.child');
            Route::get('/tree-genealogy', 'genealogy')->name('network.genealogy');
            Route::get('/tree-sponsor', 'sponsorTree')->name('network.sponsorTree');
            Route::get('/downlineMembers', 'downlineMembers')->name('network.downlineMembers');
            Route::get('referralMembers', 'referralMembers')->name('network.referralMembers');
            Route::get('/chanage-placement', 'changePlacement')->name('change.placement');
            Route::post('/chanage-placement', 'updatePlacement')->name('change.placement.store');
            Route::get('/chanage-sponsor', 'changeSponsor')->name('change.sponsor');
            Route::post('/chanage-sponsor', 'updateSponsor')->name('change.sponsor.store');
            Route::get('/step-view', 'stepView')->name('network.step.view');
            Route::get('/collapse-tree', 'collapseTree')->name('network.collapse.tree');
            Route::get('/get-more-children/{father}/{count}', 'moreChildren')->name('network.more.child');
            Route::get('/re-entries', 'reEntry')->name('network.reentry.table');
            Route::get('/get-more-sponsor-children/{sponsor}/{count}', 'moreSponsorChildren')->name('network.more.sponsor.child');

        });
        Route::controller(PaypalPaymentController::class)->group(function () {
            Route::post('/paypal/webhook', 'webhooksPayoutSuccess')->withoutMiddleware(['prefixAuth', 'Localization', 'Permission']);
            Route::post('/paypal/webhook/fail', 'webhooksPayoutFail')->withoutMiddleware(['prefixAuth', 'Localization', 'Permission']);
            Route::post('/paypal-order/create', 'create')->name('paypal.create');
            Route::post('/paypal-order/capture/', 'capture')->name('paypal.capture');
        });
        Route::controller(UserRegisterController::class)->group(function () {
            Route::get('/register/{sponsor?}/{position?}', 'registerForm')->name('register.form');
            Route::post('/register', 'userRegister')->name('user.register');
            Route::post('/user/add/payment/receipt', 'addPaymentReceipt')->name('user.add-payment-receipt');
            Route::get('/insert-Dummy', 'dummyUsers')->name('insert.dummy');
            Route::post('/stripe-client-secret', 'getClientSecret')->name('register.stripe');
            Route::post('/stripe', 'stripePost')->name('register.stripe.post');
            Route::get('/register-preview/{username}', 'preview')->name('register.preview');
            Route::get('/check-dob', 'checkDob')->name('check.dob');
            Route::get('/check-email/{email}', 'checkEmail')->name('check.email');
            Route::post('/check-mobile', 'checkMobile')->name('check.mobile');
            Route::get('/check-username', 'checkUsername')->name('check.username');
            Route::get('register-store', 'storeRegister')->name('store.register');
            Route::get('/store', 'store')->name('store.index');
        });

        Route::controller(ReportController::class)->group(function () {
            Route::get('/reports/join', 'joining')->name('report.join');
            Route::get('/reports/epin', 'epinTransfer')->name('reports.epinTransfer');
            Route::post('/reports/epin/filter', 'epinTransferFilter')->name('epinTransfer.filter');
            Route::get('report-activateDeactivate', 'actDeactReport')->name('reports.activateDeactivate');
            Route::get('/report/commission', 'commissionReport')->name('reports.commission');
            Route::get('/reports/package/upgrade', 'upgradePackage')->name('reports.upgradePackage');
            Route::get('/reports/rank/achievers', 'rankAchieversReport')->name('reports.rank-achievers');
            Route::get('/reports/rank/performance', 'rankPerformanceReport')->name('reports.rank-performance');
            Route::get('/reports/payouts', 'payoutReport')->name('reports.payouts');
            Route::get('getinvoice-details/{id}', 'getinvoiceDetails')->name('getinvoice.details');
            Route::get('reports/topearners', 'topearners')->name('reports.top-earners');
            Route::get('topEarners-single/{id}', 'singleTopEarner')->name('topEarners.single');
            Route::get('/report/total-bonus', 'totalBonusReport')->name('reports.totalbonus');
            Route::get('getTotalBonus', 'gettotalBonusReport')->name('getTotalBonus');
            Route::get('/subscription/report', 'subscriptionReport')->name('reports.subscription');
            Route::get('/getSubscription', 'getSubscriptionReport')->name('reports.getSubscription');
            Route::get('/report/purchase', 'purchaseReport')->name('reports.purchase');
            Route::get('/getPurchase', 'getPurchaseReport')->name('report.getPurchaseReport');
            Route::get('/get/user/sales/invoice/{userid}', 'getSalesInvoice')->name('report.getSalesInvoice');
            Route::get('/get/user/purchase/invoice/{userid}', 'getPurchaseInvoice')->name('report.getPurchaseInvoice');
        });
        Route::controller(BusinessController::class)->group(function () {
            Route::get('/reports-business', 'index')->name('reports.business');
            Route::get('/business-summary', 'getSumary')->name('business.getSumary');
            Route::get('/business-transaction', 'businessTransaction')->name('business.transaction');
        });

        Route::controller(ShoppingCartController::class)->group(function () {
            Route::get('/products', 'index')->name('products.view');
            Route::get('/product-details/{id?}', 'productDetails')->name('product-details');
            Route::get('/add-to-cart/{package_id?}', 'addToCart')->name('add-to-cart');
            Route::get('/cart-view', 'cartIndex')->name('cart.view');
            Route::post('/cart-update', 'cartUpdate')->name('cart.update');
            Route::get('/cart/delete/{package_id}', 'cartDelete')->name('cart.delete');
            Route::get('/checkout', 'checkout')->name('cart.checkout');
            Route::post('/cart/add-new-address', 'addNewAddress')->name('cart.add-new-address');
            Route::delete('/cart/address-delete/{address}', 'cartAddressDelete')->name('cart.address-delete');
            Route::get('/cart/defacheckout.submitult-address/{id}', 'cartDefaultAddress')->name('cart.default-address');
            Route::post('/checkout/submit', 'checkoutSubmit')->name('checkout.submit');
            Route::get('/checkout/invoice', 'checkoutInvoice')->name('checkout.invoice');
            Route::get('order-approval', 'pendingorderApproval')->name('order.approval');
            Route::get('get-pendingOrders', 'getpendingOrders')->name('getpendingOrders');
            Route::get('getReceipt/{id}', 'getOrderReceipt')->name('getReceipt');
            Route::post('order-approval', 'approveOrders')->name('order.approve');
            Route::post('/cart/add/payment/receipt', 'addPaymentReceipt')->name('cart.add-payment-receipt');
        });

        Route::controller(PrivilegedUserController::class)->group(function () {
            Route::get('/privileged-user/index', 'index')->name('privileged-user.index');
            Route::post('/privileged-user/store', 'store')->name('privileged-user.store');
            Route::get('/privileged-user/filter', 'filter')->name('privileged-user.filter');
            Route::put('/employee-dashboard/update', 'employeeDashboardUpdate')->name('employee-dashboard.update');
            Route::delete('/employee/delete/{employee_id}', 'employeeDelete')->name('employee.delete');
            Route::get('/employee/edit/{id?}', 'edit')->name('employee.edit');
            Route::post('/employee/update', 'update')->name('employee.update');
            Route::get('/employee/edit-dashboard/{id?}', 'editDashboard')->name('employee.edit-dashboard');
            Route::get('/employee/change-password-view/{id?}', 'changePasswordView')->name('employee.change-password-view');
            Route::post('/employee/change-password', 'changePassword')->name('employee.change-password');
            Route::get('/employee/edit-menu/{id?}', 'editMenu')->name('employee.edit-menu');
            Route::put('/employee-menu/update', 'employeeMenuUpdate')->name('employee-menu.update');
            Route::get('/employee/activities', 'getEmployeeActivities')->name('get.employee.activities');
        });
        Route::post('/check-password', [Ajax\AdminRegisterController::class, 'password'])->name('check.password');

        Route::controller(MemberListController::class)->group(function () {
            Route::get('/memberlist-view', 'index')->name('memberlist.view');
            // Route::post('/memberlist', 'memberList')->name('memberlist.list');
            Route::get('/memberlist', 'memberList')->name('memberlist.list');
            Route::post('/memberlist-userupdate', 'userUpdate')->name('memberlist.userupdate');
            Route::get('/package/upgrade/reports/pending', 'getPendingUpgrade')->name('package.upgrade.pending');
            Route::get('/package/renewal/pending', 'getPendingRenewal')->name('package.renewal.pending');
            Route::post('/package/upgrade/approve/{id?}', 'approve')->name('package.upgrade.approve');
            Route::post('/package/renewal/approve/{id?}', 'renewalApprove')->name('package.renewal.approve');
            Route::get('/package-upgrade', 'packageupgradeapproval')->name('package-upgrade.view');
            Route::get('/package-renewal', 'packagerenewalapproval')->name('package-renewal.view');
        });
        Route::controller(Ajax\ReferalCommissionController::class)->group(function () {
            Route::get('/referral', 'referralCommission')->name('ajax.referral');
            Route::get('/active-rank', 'referralRank')->name('ajax.referralRank');
            Route::get('/level-commission', 'levelCommissionPackage')->name('ajax.levelcommission');
        });
        Route::controller(Ajax\UserRegisterController::class)->group(function () {
            Route::get('/sponsor', 'sponsorUsername')->name('ajax.sponsorName');
            Route::get('/replica/sponsor/{sponsor?}', 'replicaSponsorName')->name('ajax.replica-sponsorName');
            Route::get('/state', 'state')->name('ajax.state');
            Route::get('/country/{country?}', 'getstate')->name('country.state');
            Route::get('/package-total/{id?}', 'totalAmount')->name('ajax.totalAmount');
            Route::get('/check/leg-availability/{leg?}/{sponsor?}', 'checkLegAvailability')->name('check.legAvailability');
            Route::post('/register/epin/availability', 'checkEpinAvailability')->name('register.check.epin');
            Route::post('/checkavailability', 'checkEwalletAvailability')->name('register.check.ewallet');
            Route::delete('/remove/reciept', 'removeBankReciept')->name('remove.bank.reciept');
            Route::get('/generate/username', 'generateDynamicUsername')->name('generate.dynamic.username');
        });

        Route::controller(CoreInfController::class)->group(function () {
            Route::get('get/users', 'getUsers')->name('get.users');
            Route::get('get/pin/amounts', 'getPinAmounts')->name('get.pinAmounts');
            Route::get('get/employees', 'getEmployees')->name('get.employees');
            Route::get('get/epin', 'getEpin')->name('get.epin');
            Route::get('get/ticket/ids', 'getTickets')->name('get.tickets');
            Route::get('/get/preset/countries', 'getPresetCountries')->name('get.preset.countries');
        });

        Route::controller(PayoutSummaryController::class)->group(function () {
            Route::get('/payout', 'index')->name('reports.payout');
            Route::get('/payout/reports/releases', 'getPayoutReleases')->name('payout.reports.release');
            Route::get('/payout/reports/sumary', 'payoutSummary')->name('payout.reports.summary');
            Route::post('/payout/release/{checkedItems}', 'releaseManualPayouts')->name('payout.release.request');
            Route::get('/payout/process/payment', 'processPayment')->name('payout.process.payment');
            Route::post('/payout/approve/{id?}', 'approveProcessPayment')->name('payout.approve.payment');
            Route::post('/payout/approve/user/request/{checkedItems}', 'releaseUserRequestPayouts')->name('payout.approve.user.request');
            Route::post('/payout/reject/user/request/{checkedItems}', 'rejectPayoutReleaseRequest')->name('payout.reject.user.request');
            Route::get('/payout/amounts', 'getSummaryAmounts')->name('payout.summary.amounts');
        });

        Route::controller(LanguageController::class)->group(function () {
            Route::get('/language', 'index')->name('language');
            Route::patch('/language-default/{language}', 'setDefault')->name('language.set.default');
            Route::patch('/language/manage/{language}', 'edit')->name('language.update');
            Route::patch('/user-language', 'setUserLang');
        });


        Route::controller(BulkRegisterController::class)->group(function () {
            Route::get('/excel/register', 'index')->name('user.bulkRegister');
            Route::post('/excel/register', 'store')->name('user.bulkRegister.store');
        });
        Route::controller(RoiController::class)->group(function () {
            Route::get('/hyip-roi', 'index')->name('hyip-roi');
        });
        Route::controller(PlanSettingsController::class)->group(function () {
            Route::get('/settings/plan/{stairstep?}', 'index')->name('settings.plan');
            Route::post('/settings/update/{id?}', 'matrixConfigUpdate')->name('matrix.config.update');
            Route::post('/settings/donation/', 'donationConfigUpdate')->name('donation.config.update');
        });

        Route::controller(DonationController::class)->group(function () {
            Route::get('/received/donation', 'receivedDonation')->name('reports.receivedDonation');
            Route::get('/report/donation', 'receivedDonationReport')->name('receivedDonation');
            Route::get('/given/donation', 'givenDonation')->name('reports.givenDonation');
            Route::get('/report/givendonation', 'givenDonationReport')->name('givenDonation');
            Route::get('/missed/donation', 'missedDonation')->name('reports.missedDonation');
            Route::get('/report/missedDonation', 'missedDonationReport')->name('missedDonation');
            Route::get('/manage/user/level', 'manageUserLevel')->name('donation.manageUserLevel');
            Route::post('/update/user/level/{id?}', 'updateUserLevel')->name('donation.updateUserLevel');
        });

        Route::controller(StairStepController::class)->group(function () {
            Route::put('/stairstep/config/update', 'configUpdate')->name('stairstep.config.update');
            Route::post('/stairstpe/store/{id?}', 'storeStepConfig')->name('stairstep.config.store');
            Route::get('/commission/report', 'commissionReport')->name('stairstep.commission.report');
            Route::get('/reports/commission', 'getCommissionReport')->name('get.commission.report');
            Route::get('/commission/override', 'overrideCommission')->name('stairstep.commission.override');
            Route::get('/reports/override', 'getOverrideCommissionReport')->name('get.override.report');
        });

        Route::controller(PartyPlanController::class)->group(function () {
            Route::get('/create/party', 'createParty')->name('party.create');
            Route::post('/party/store', 'partyStore')->name('party.store');
            Route::get('/host/management', 'hostManager')->name('host.management');
            Route::get('/host/management/edit/{id?}', 'editHost')->name('host.management.edit');
            Route::post('/host/store/{id?}', 'storeHost')->name('host.store');
            Route::put('/host/disable', 'disableHost')->name('host.disable');
            Route::get('/promote/party/{id?}', 'promoteParty')->name('promote.party');
        });

        Route::controller(PartyPortalController::class)->group(function () {
            Route::get('/party/portal', 'index')->name('party.partyPortal');
            Route::get('/party/invite/guest', 'inviteParty')->name('invite.guest.party');
            Route::post('/set/myparty', 'partySessionSet')->name('set.party.session');
            Route::post('/invite/sent', 'sentInvite')->name('inviteto.party');
            Route::get('/guest/orders', 'guestOrders')->name('guest.orders');
            Route::get('/guest/order/select/product/{id?}', 'selectProduct')->name('guest.selectOrder');
            Route::post('/product/add/cart/{guest_id?}/{product_id?}', 'productToCart')->name('guest.addToCart');
            Route::post('/clear/cart/{guest_id?}/', 'clearCart')->name('guest.clearCart');
            Route::post('/complete/guest/order/{guest_id?}', 'completeGuestOrder')->name('guest.completeOrder');
            Route::get('/edit/guest/order/{guest_id?}', 'editGuestOrder')->name('edit.guest.order');
            Route::put('/update/guest/order/{guest_id?}', 'updateGuestOrder')->name('update.guest.order');
            Route::delete('/guest/order/delete/{id?}', 'deleteGuestOrder')->name('delete.guest.order');
            Route::put('/process/guest/order/{id?}', 'processGuestOrder')->name('process.guest.order');
            Route::get('/party/guest/view/order/{id?}', 'viewApprovedOrder')->name('view.approved.order');
            Route::put('/close/party/{id?}', 'closeParty')->name('close.party');
        });

        Route::controller(GuestManagementController::class)->group(function () {
            Route::get('/guest/manager', 'index')->name('guest.index');
            Route::get('/guest/edit/{id?}', 'edit')->name('guest.edit');
            Route::post('/guest/store/{id?}', 'store')->name('guest.store');
            Route::put('/guest/disable', 'delete')->name('guest.delete');
        });

        Route::controller(PromotionalToolsController::class)->group(function () {
            Route::get('/promotional-tools', 'index')->name('promotionalTools.index');
            Route::post('/add-invites', 'store')->name('invites.store');
            Route::post('/edit-invites', 'update')->name('invites.update');
            Route::post('/delete-invites', 'deleteInvites')->name('invites.delete');
            Route::post('/invites-emptyStatus', 'checkEmptyStatus')->name('invites.emptyStatus');
        });

        Route::controller(AutoResponderController::class)->group(function () {
            Route::get('auto-responder', 'index')->name('responder.index');
            Route::post('auto-responder/store', 'store')->name('responder.store');
            Route::post('auto-responder/responder-inactive', 'responderInactive')->name('responder.delete');
            Route::post('auto-responder/update', 'updateResponderData')->name('responder.update');
        });

        Route::controller(SubscriptionController::class)->group(function () {
            Route::get('subscriptions/renewal/{id?}', 'index')->name('subscriptions.renewal.index');
            Route::post('/subscriptions/renewal/submit', 'renewSubmit')->name('subscriptions.renew.submit');
            Route::post('/renew/add/payment/receipt', 'addPaymentReceipt')->name('renew.add-payment-receipt');
        });
    });

    Route::controller(KycController::class)->group(function () {
        Route::get('profile/kyc/', 'index')->name('profile.kycDetails');
        Route::get('kyc-details', 'kycDetails')->name('kyc.details');
        Route::post('/approve-kyc/{checkedItems}', 'approvekyc')->name('approve.kyc');
        Route::post('/reject-kyc/{checkedItems}', 'rejectkyc')->name('reject.kyc');
        Route::get('/kyc-image/{id}', 'getUserKycImage')->name('kyc.image');
    });

    Route::controller(Report\ExcelController::class)->group(function () {
        Route::get('/export-activedeactiveexcel', 'exportActiveDeactiveExcel')->name('export.activedeactiveexcel');

        Route::get('/export-rankachieversexcel', 'exportRankAchieversExcel')->name('export.rankachieversexcel');
        Route::post('/export-topearnersexcel', 'exporttopearnersExcel')->name('export.topearnersexcel');
        Route::get('/export-packageupgrade', 'exportPackageUpgradeExcel')->name('export.packageupgradeexcel');
        Route::post('/export-joiningreportexcel', 'joinReport')->name('export.joiningreportexcel');
        Route::get('/export-commissionreportexcel', 'commissionReport')->name('export.commissionreportexcel');
        Route::get('/export-profiledatereportexcel', 'profileDateReport')->name('export.profiledatereportexcel');
        Route::get('/export-profilereportexcel', 'profileReport')->name('export.profilereportexcel');
        Route::get('/export-bonusreportexcel', 'bonusreportexcel')->name('export.bonusreportexcel');
        Route::post('/export-payoutPendingExcel', 'payoutPendingExcel')->name('export.payoutPendingExcel');
        Route::get('/export-purchase-report', 'purchaseReportExcel')->name('export.purchaseReport');
        Route::get('/export-subscription-report', 'subscriptionReportExcel')->name('excel.subscriptionReport');
        Route::get('/epin-transfer-report', 'epinTransferReportExcel')->name('export.epinTransferReport');
        Route::get('/export-payout-pending-userAmount-report', 'payoutPendingUserAmountExcel')->name('export.excel.ManualPayoutPending');
        Route::get('/export-payout-pending-request-report', 'payoutPendingUserRequestExcel')->name('export.excel.userRequestPayoutPending');
        Route::get('/export-payout-release-report', 'payoutReleaseExcel')->name('export.excel.payoutRelease');
    });

    Route::controller(Report\CsvController::class)->group(function () {
        Route::get('/export-activedeactivecsv', 'exportActiveDeactiveCsv')->name('export.activedeactivecsv');
        Route::get('/export-rankachieverscsv', 'exportRankAchieversCsv')->name('export.rankachieverscsv');
        Route::get('/export-topearnerscsv', 'exporttopearnersCsv')->name('export.topearnerscsv');
        Route::get('/export-packageupgradecsv', 'exportPackageUpgradeCsv')->name('export.packageupgradecsv');
        Route::get('/export-commissionreportcsv', 'exportCommissionReportCsv')->name('export.commissionreportcsv');
        Route::get('/export-joiningreportcsv', 'exportJoiningReportCsv')->name('export.joiningreportcsv');
        Route::get('/export-profilereportcsv', 'exportProfileReport')->name('export.profilereportcsv');
        Route::get('/export-bonusreportcsv', 'exportBonusReportCsv')->name('export.bonusreportcsv');
        Route::get('/export-payoutpendingreportcsv/{status?}', 'exportPayoutPendingcsv')->name('export.payoutpendingreportcsv');
        Route::get('/export-purchase-report/csv', 'exportPurchaseCSV')->name('export.purchaseReport.csv');
        Route::get('/export-subscription-report/csv', 'exportSubscriptionCSV')->name('export.subscriptionReport.csv');
        Route::get('/export-epin-transfer-report', 'exportTransferReportCSV')->name('export.epinTransferReportcsv');
        Route::get('/export-payout-pending-userAmount-report/csv', 'exportPayoutPendingManualCsv')->name('export.ManualPayoutPending.csv');
        Route::get('/export-payout-pending-userRequest-report/csv', 'exportPayoutPendingUserRequestCsv')->name('export.userRequestPayoutPending.csv');
        Route::get('/export-payout-release/csv', 'exportPayoutReleaseCsv')->name('export.payoutRelease.csv');
    });

    Route::controller(ConfigurationController::class)->group(function () {
        Route::get('/manage-menus', 'mangeMenu')->name('manage.menu');
        Route::post('/menu-update', 'menuUpdate')->name('menu.update');
        Route::get('/commission/status', 'manageCommission')->name('manage.commission');
        Route::get('/manage-modules', 'manageModuleStatus')->name('manage.modules');
        Route::post('/update-modules', 'updateModules')->name('update.modules');
    });

    Route::controller(TwofactorController::class)->group(function () {
        Route::get('/two-factor-auth', 'index')->name('two.factort.auth');
        Route::post('/two-factor-verify', 'verifyCode')->name('twofactor.verify');
        Route::get('/two-factor-reset', 'resetTwoFactor')->name('twofactor.reset');
        Route::get('/two-factor/reset', 'sendTwoFactorMail')->name('twoFA.reset');
        Route::get('/two-factor/success', 'TwoFactorSuccess')->name('twoFA.success');
    });
    Route::controller(NotificationController::class)->group(function () {
        Route::get('/read-notification/{id?}', 'readSingleNotification')->name('read.notification');
        Route::get('/read-all-notification', 'readAllNotification')->name('read.all');
        Route::get('/notifications', 'getNotificationsMOre')->name('notification.more');
    });
    Route::get('/insert-user-dummy/{count}', [DummyController::class, 'insert']);

    Route::controller(MonolineConfigController::class)->group(function () {
        Route::get('plan/configuration', 'index')->name('monoline.index');
        Route::post('/plan/configuration/{id?}', 'configUpdate')->name('monoline.update');
        Route::post('/plan/reentry-icon', 'treeIconUpdate')->name('monoline.treeIcon.update');
    });

    Route::controller(CleanupController::class)->group(function () {
        Route::get('/cleanup', 'cleanUp')->name('cleanup');
        Route::get('/insert-dummy-users/{count?}', 'insertDummy');
    });
});
Route::fallback(function () {
    return view('layouts.fallback');
});

