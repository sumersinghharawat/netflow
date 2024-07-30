import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { useDispatch, useSelector } from "react-redux";
import {
    AppLayout,
    DashboardDetails,
    DashboardExpenses,
    DashboardTiles,
    DashboardUserProfile,
    GetGraph,
    NotificationData,
    PackageOverview,
    RankOverview,
    ReadAllNotification,
    TopRecruiters
} from "../store/actions/dashboardAction";
import {
    AdditionalDetails,
    BankDetailsUpdate,
    changePassword,
    changeTransactionPassword,
    ContactDetailsUpdate,
    deleteKycFile,
    deleteProfileAvatar,
    fetchProfile,
    KycDetails,
    KycUpload,
    loginUser,
    logout,
    PaymentDetails,
    PersonalDetailsUpdate,
    updateCurrency,
    updateLanguage,
    updateProfileAvatar
} from "../store/actions/userAction";
import {
    setAppLayout,
    setDashboardOne,
} from "../store/reducers/dashboardReducer";
import {
    setConversionFactors,
    setIsAuthenticated,
    setLoginResponse,
    setProfile,
    setSelectedCurrency,
    setSelectedLanguage,
    updateBank,
    updateContact,
} from "../store/reducers/userReducer";
import { useNavigate } from "react-router";
import {
    GenealogyActions,
    SponserTreeActions,
    TreeViewActions,
    downlineMembersActions,
    ReferralMembersActions
} from "../store/actions/treeAction";
import {
    enableBackToParent,
    enableSponserBackToParent,
    setGenealogyTreeList,
    setSponserTreeList,
    setTreeViewList,
    updateSponserTree,
    updateSponserTreeList,
    updateTreeNode,
    updateTreeViewList,
    updateUnilevelGenealogyTree,
} from "../store/reducers/treeReducer";
import {
    EwalletBalance,
    FundTransfer,
    MyEarnings,
    PurchaseHistory,
    Statement,
    Tiles,
    TransferHistory,
} from "../store/actions/ewalletAction";
import {
    PayoutRequestApi,
    PayoutRequestDetails,
    PayoutTiles,
    TilesAndDetails,
} from "../store/actions/payoutAction";
import {
    EpinList,
    EpinPartials,
    EpinPendingRequest,
    EpinPurchase,
    EpinRefund,
    EpinRequest,
    EpinTiles,
    EpinTransfer,
    EpinTransferHistory,
    PurchasedEpinList,
} from "../store/actions/epinAction";
import { callCheckIsPresent, callDemoVisitorData, callResendOtp, callVerifyOtp } from "../store/actions/demoVisitorAction"
import { toast } from "react-toastify";
import { RegisterFields, RegisterFieldCheck, TranssPassCheck, CreateRegisterLink, CreateStoreLink, RegisterUser, BankUpload, LetterPreview, deleteBankReceipt,CreatePaymentIntent, GetPaymentGatewayKey } from "../store/actions/registerAction";
import { useTranslation } from "react-i18next";
import { deleteReplicaBanner, getDownloadMaterials, getFaqs, getLeads, getNews, getNewsById, getReplicaBanner, searchLeads, updateLead, uploadReplicaBanner } from "../store/actions/toolsAction";
import { DefaultAddressChange, PlaceRepurchaseOrder, ProductDetails, RepurchaseInvoice, RepurchaseReport, addAddress, addToCart, callPaymentMethods, decrementCartItem, getAddress, getCartItems, getRepurchaseItems, removeAddress, removeCartItem } from "../store/actions/shopping";
import { ReplicaBankRecieptDelete, ReplicaBankUploadReceipt, ReplicaContactUpload, ReplicaHome, ReplicaRegisterFields, ReplicaRegisterPost, getApiKey, replicaFieldCheck } from "../store/actions/replicaAction";
import { setCompanyDetails, setRegisterLink, setTermsAndPolicy } from "../store/reducers/replica";
import { AddLcpLead, getCompanyDetails, getReplicaApi } from "../store/actions/lcp";
import { UpgradeActions } from "../store/actions/upgradeAction";
import { RenewActions } from "../store/actions/renewAction";
import { ChangeForgotPassword, ForgotPassword, VerifyForgotPassword } from "../store/actions/authAction";
import { AdminInboxes, DeleteMail, Inboxes, ReplyMail, SendInternalMail, SentMail, SingleMail, replicaInbox } from "../store/actions/mailAction";
import { SponserTreeService } from "../services/tree/network";
import { addMail, setMails } from "../store/reducers/mailBoxReducer";
import { createTicket, getTicketDetails, getTicketFaqs, getTicketPartials, getTicketReplies, getTickets, getTrackId, ticketReply, ticketTimeline } from "../store/actions/ticketAction";
import LoginService from "../services/auth/Login";
import { AddCrmLead, AddFollowUp, CrmGraph, CrmTiles, EditCrmLead, FollowupToday, GetCountries, LeadDetails, MissedFollowup, RecentLeads, ViewLeads, addNextFollowUp, crmTimeline } from "../store/actions/crmAction";

export const ApiHook = {
    // ---------------------------------------- Login -----------------------------------------

    CallLoginUser: () => {
        const dispatch = useDispatch();
        const navigate = useNavigate();
        const response = useMutation((credentials) => loginUser(credentials), {
            onSuccess: (response) => {
                if (response.status) {
                    dispatch(setIsAuthenticated(true));
                    dispatch(setLoginResponse(response));
                    navigate("/dashboard", { replace: true });
                }
            },
        });
        return response;
    },
    CallLogout: () => {
        const dispatch = useDispatch();
        const navigate = useNavigate();
        const response = useMutation(() => logout(), {
            onSuccess: (data) => {
                if (data.status) {
                    dispatch(setLoginResponse(null));
                    dispatch(setIsAuthenticated(false));
                    localStorage.clear();
                    navigate("/login");
                }
            }
        })
        return response
    },
    CallForgotPassword: () => {
        const response = useMutation((data) => ForgotPassword(data))
        return response
    },
    CallVerifyForgotPassword: (data) => {
        const response = useQuery({
            queryKey: ['verify-forgotPassword'],
            queryFn: () => VerifyForgotPassword(data)
        })
        return response
    },
    CallChangeForgotPassword: () => {
        const response = useMutation((data) => ChangeForgotPassword(data));
        return response
    },
    CallCheckIsPresent: () => {
        const response = useQuery({
            queryKey: ["check-is-present"],
            queryFn: callCheckIsPresent
        });
        return response;
    },
    CallAddDemoVisitor: () => {
        const response = useMutation((data) => callDemoVisitorData(data));
        return response;
    },
    CallResendOtp: () => {
        const response = useMutation((data) => callResendOtp(data));
        return response;
    },
    CallVerifyOtp: () => {
        const response = useMutation((data) => callVerifyOtp(data));
        return response;
    },
    CallForgotPassword: () => {
        const response = useMutation((data) => LoginService.forgotPassword(data));
        return response;
    },
    // ---------------------------------------- Dashboard -----------------------------------------

    CallAppLayout: () => {
        const dispatch = useDispatch();
        const defaultCurrency = useSelector((state) => state.user?.loginResponse?.defaultCurrency);
        const response = useQuery({
            queryKey: ["app-layout"],
            queryFn: AppLayout,
            onSuccess: (data) => {
                dispatch(setAppLayout(data));
                dispatch(setSelectedCurrency(data?.user?.defaultCurrency ? data?.user?.defaultCurrency : null));
                dispatch(setSelectedLanguage(data?.user?.defaultLang ? data?.user?.defaultLang : null));
                dispatch(
                    setConversionFactors({
                        currencies: data?.currencies,
                        selectedCurrency: data?.user?.defaultCurrency ? data?.user?.defaultCurrency : JSON.parse(defaultCurrency),
                        defaultCurrency: JSON.parse(defaultCurrency)
                    }))
            },
        });
        return response;
    },
    CallDashboardRight: (dashboardCheck, setDashboardCheck) => {
        const response = useQuery({
            queryKey: ["dashboard-user-profile"],
            queryFn: DashboardUserProfile,
            onSuccess: () => {
                setDashboardCheck(false)
            },
            enabled: !!(dashboardCheck)
        });
        return response;
    },
    CallDashboardTiles: () => {
        const response = useQuery({
            queryKey: ["dashboard-tiles"],
            queryFn: DashboardTiles,
        });
        return response;
    },
    CallGraphFilter: (selectedFilter) => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ["get-graph", selectedFilter],
            queryFn: () => GetGraph(selectedFilter),
            onSuccess: (data) => {
                dispatch(setDashboardOne(data));
            },
        });
        return response;
    },
    CallCurrencyUpdation: ({ selectedCurrency }) => {
        const dispatch = useDispatch();
        const mutation = useMutation(
            (id) => updateCurrency(id),
            {
                onSuccess: () => {
                    dispatch(setSelectedCurrency(selectedCurrency));
                },
            }
        );
        return mutation
    },
    CallLanguageUpdation: ({ selectedLanguage }) => {
        const dispatch = useDispatch();
        const mutation = useMutation(
            (id) => updateLanguage(id),
            {
                onSuccess: () => {
                    dispatch(setSelectedLanguage(selectedLanguage));
                },
            }
        );
        return mutation
    },
    CallNotificationData: (notificationCheck, setNotificationCheck) => {
        const response = useQuery({
            queryKey: ['notification-data'],
            queryFn: NotificationData,
            onSuccess: () => {
                setNotificationCheck(false)
            },
            enabled: !!notificationCheck
        })
        return response
    },
    CallReadAllNotification: () => {
        const queryClient = useQueryClient();
        const response = useMutation(ReadAllNotification, {
            onSuccess: (res) => {
                if (res?.status) {
                    queryClient.invalidateQueries({ queryKey: ["app-layout"] });
                }
            }
        })
        return response
    },
    CallDashboardDetails: () => {
        const response = useQuery({
            queryKey: ["dashboard-details"],
            queryFn: DashboardDetails
        })
        return response
    },
    CallTopRecruiters: (recruitersCheck, setRecruitersCheck) => {
        const response = useQuery({
            queryKey: ["top-recruiters"],
            queryFn: TopRecruiters,
            onSuccess: () => {
                setRecruitersCheck(false)
            },
            enabled: !!(recruitersCheck)
        })
        return response
    },
    CallPackageOverview: (packageCheck, setPackageCheck) => {
        const response = useQuery({
            queryKey: ["package-overview"],
            queryFn: PackageOverview,
            onSuccess: () => {
                setPackageCheck(false)
            },
            enabled: !!(packageCheck)
        })
        return response
    },
    CallRankOverview: (rankCheck, setRankCheck) => {
        const response = useQuery({
            queryKey: ["rank-overview"],
            queryFn: RankOverview,
            onSuccess: () => {
                setRankCheck(false)
            },
            enabled: !!(rankCheck)
        })
        return response
    },
    CallDahboardExpenses: (expenseCheck, setExpenseCheck) => {
        const response = useQuery({
            queryKey: ["dashboard-expenses"],
            queryFn: DashboardExpenses,
            onSuccess: () => {
                setExpenseCheck(false)
            },
            enabled: !!(expenseCheck)
        })
        return response
    },
    // ---------------------------------------- Profile -----------------------------------------

    CallProfile: () => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ["profile"],
            queryFn: fetchProfile,
            onSuccess: (data) => {
                dispatch(setProfile(data));
            },
        });
        return response;
    },
    CallUpdatePersonalDetails: () => {
        const mutation = useMutation((profileDetails) => PersonalDetailsUpdate(profileDetails))
        return mutation;
    },
    CallUpdateContactDetails: (contactDetails) => {
        const dispatch = useDispatch();
        const mutation = useMutation(
            (contactDetails) => ContactDetailsUpdate(contactDetails),
            {
                onSuccess: (response) => {
                    if (response.status) {
                        dispatch(
                            updateContact({
                                contactDetails: contactDetails,
                            })
                        );
                    }
                },
            }
        );
        return mutation;
    },

    CallUpdateBankDetails: (bankDetails) => {
        const dispatch = useDispatch();
        const mutation = useMutation(
            (bankDetails) => BankDetailsUpdate(bankDetails),
            {
                onSuccess: (response) => {
                    if (response.status) {
                        dispatch(
                            updateBank({
                                bankDetails: bankDetails,
                            })
                        );
                    }
                },
            }
        );
        return mutation;
    },
    CallUpdateProfilePicture: () => {
        const { t } = useTranslation()
        const queryClient = useQueryClient();
        const mutation = useMutation(
            (profilePic) => updateProfileAvatar(profilePic),
            {
                onSuccess: (response) => {
                    if (response.status) {
                        toast.success(t(response?.data?.message));
                        queryClient.invalidateQueries({ queryKey: ["profile"] })
                        queryClient.invalidateQueries({ queryKey: ["app-layout"] })
                    } else if (response?.data?.code) {
                        toast.error(t(response?.data?.description))
                    } else {
                        toast.error(t(response?.data?.message))
                    }
                }
            }
        );
        return mutation;
    },
    CallAdditionalDetails: () => {
        const response = useMutation((additionalDetails) => AdditionalDetails(additionalDetails))
        return response
    },
    CallPaymentDetails: () => {
        const response = useMutation((paymentDetails) => PaymentDetails(paymentDetails))
        return response
    },
    CallKycDetails: () => {
        const response = useQuery({
            queryKey: ['kyc-details'],
            queryFn: KycDetails
        })
        return response
    },
    CallKycUploads: () => {
        const response = useMutation((files) => KycUpload(files))
        return response
    },
    CallDeleteKycFiles: () => {
        const { t } = useTranslation();
        const queryClient = useQueryClient();
        const response = useMutation((filesId) => deleteKycFile(filesId), {
            onSuccess: (response) => {
                if (response.status) {
                    queryClient.invalidateQueries({ queryKey: ["kyc-details"] })
                    toast.success(t(response?.data));
                }
            }
        })
        return response
    },
    CallDeleteProfileAvatar: () => {
        const { t } = useTranslation();
        const queryClient = useQueryClient();
        const response = useMutation(deleteProfileAvatar, {
            onSuccess: (response) => {
                if (response.status) {
                    queryClient.invalidateQueries({ queryKey: ["profile"] })
                    queryClient.invalidateQueries({ queryKey: ["app-layout"] })
                    toast.success(t(response?.data));
                }
            }
        })
        return response
    },
    CallChangePassword: () => {
        const response = useMutation((body) => changePassword(body))
        return response
    },
    CallChangeTransactionPassword: () => {
        const response = useMutation((body) => changeTransactionPassword(body))
        return response
    },


    // ---------------------------------------- Ewallet -----------------------------------------

    CallEwalletTiles: () => {
        const response = useQuery({
            queryKey: ["ewallet-tiles"],
            queryFn: Tiles,
        });
        return response;
    },
    CallEwalletStatement: (page, itemsPerPage, selectStatement) => {
        const response = useQuery({
            queryKey: ["statement", page, itemsPerPage, selectStatement],
            queryFn: () => Statement(page, itemsPerPage),
        });
        return response;
    },

    CallTransferHistory: (
        page,
        itemsPerPage,
        selectedPageCheck,
        setSelectedPageCheck,
        selectedCategory,
        startDate = '',
        endDate = ''
    ) => {
        const response = useQuery({
            queryKey: ["transfer-history"],
            queryFn: () => TransferHistory(page, itemsPerPage, selectedCategory, startDate, endDate),
            onSuccess: () => {
                setSelectedPageCheck(false);
            },
            enabled: !!selectedPageCheck,
        });
        return response;
    },
    CallPurchaseHistory: (
        page,
        itemsPerPage,
        selectedPageCheck,
        setSelectedPageCheck
    ) => {
        const response = useQuery({
            queryKey: ["purchase-history"],
            queryFn: () => PurchaseHistory(page, itemsPerPage),
            onSuccess: () => {
                setSelectedPageCheck(false);
            },
            enabled: !!selectedPageCheck,
        });
        return response;
    },
    CallMyEarnings: (
        page,
        itemsPerPage,
        selectedPageCheck,
        setSelectedPageCheck,
        selectedCategory,
        startDate = '',
        endDate = ''

    ) => {
        const response = useQuery({
            queryKey: ["my-earnings"],
            queryFn: () => MyEarnings(page, itemsPerPage, selectedCategory, startDate, endDate),
            onSuccess: () => {
                setSelectedPageCheck(false);
            },
            enabled: !!selectedPageCheck,
        });
        return response;
    },
    CallFundTransfer: () => {
        const response = useMutation((data) => FundTransfer(data), {
            onSuccess: (data) => {
                if (data.status === 200) {
                    toast.success(data.data.data)
                }
            }
        });
        return response;
    },
    // ---------------------------------------- Payout -----------------------------------------

    CallPayoutDetails: (page, itemsPerPage, type) => {
        const response = useQuery({
            queryKey: ["payout-details", page, itemsPerPage, type],
            queryFn: () => TilesAndDetails(page, itemsPerPage, type),
        });
        return response;
    },
    CallPayoutRequestDetails: () => {
        const response = useQuery({
            queryKey: ["payout-request-details"],
            queryFn: PayoutRequestDetails,
        });
        return response;
    },
    CallPayoutRequest: () => {
        const response = useMutation((data) => PayoutRequestApi(data), {
            onSuccess: (data) => {
                if (data.status === 200) {
                    toast.success(data.data.data)
                }
            }
        });
        return response;
    },
    CallPayoutTiles: () => {
        const response = useQuery({
            queryKey: ['payout-tiles'],
            queryFn: PayoutTiles
        })
        return response
    },
    // ---------------------------------------- Epin -------------------------------------------

    CallEpinTiles: () => {
        const response = useQuery({
            queryKey: ["epin-tiles"],
            queryFn: EpinTiles,
        });
        return response;
    },
    CallEpinList: (page, perPage, epin, amount, status) => {
        const response = useQuery({
            queryKey: ["epin-lists", page, perPage, epin, amount, status],
            queryFn: () => EpinList(page, perPage, epin, amount, status),
        });
        return response;
    },
    CallEpinPendingRequest: (
        page,
        perPage,
        selectedPageCheck,
        setSelectedPageCheck
    ) => {
        const response = useQuery({
            queryKey: ["epin-pending"],
            queryFn: () => EpinPendingRequest(page, perPage),
            onSuccess: () => {
                setSelectedPageCheck(false);
            },
            enabled: !!selectedPageCheck,
        });
        return response;
    },
    CallEpinHistory: (page, perPage, selectedPageCheck, setSelectedPageCheck) => {
        const response = useQuery({
            queryKey: ["epin-history"],
            queryFn: () => EpinTransferHistory(page, perPage),
            onSuccess: () => {
                setSelectedPageCheck(false);
            },
            enabled: !!selectedPageCheck,
        });
        return response;
    },
    CallEpinPurchase: () => {
        const response = useMutation((data) => EpinPurchase(data), {
            onSuccess: (data) => {
                if (data.status === 200) {
                    toast.success(data.data.data)
                }
            },
            onError: () => {
                toast.error('Operation failed');
            },
        });
        return response;
    },
    CallEpinRequest: () => {
        const response = useMutation((data) => EpinRequest(data), {
            onSuccess: (data) => {
                if (data.status === 200) {
                    toast.success(data.data.data)
                }
            }
        });
        return response;
    },
    CallEpinTransfer: () => {
        const response = useMutation((data) => EpinTransfer(data), {
            onSuccess: (data) => {
                if (data.status === 200) {
                    toast.success(data.data.data)
                }
            }
        });
        return response;
    },
    CallEpinRefund: () => {
        const response = useMutation((data) => EpinRefund(data));
        return response;
    },
    CallPurchasedEpinList: () => {
        const response = useQuery({
            queryKey: ["purchased-epin-list"],
            queryFn: () => PurchasedEpinList(),
            onSuccess: (response) => {
            }
        });
        return response
    },
    CallEpinPartials: () => {
        const response = useQuery({
            queryKey: ['epin-partials'],
            queryFn: EpinPartials
        })
        return response
    },

    // ---------------------------------------- Tree -----------------------------------------

    CallGenealogyTreeList: (selectedUserId = "", doubleClickedUser, userName = "") => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ["genealogy-tree-list", selectedUserId, doubleClickedUser, userName],
            queryFn: () =>
                GenealogyActions.getTreelist(
                    selectedUserId ? selectedUserId : doubleClickedUser, userName
                ),
            onSuccess: (res) => {
                if (res?.status) {
                    if (doubleClickedUser || userName) {
                        dispatch(setGenealogyTreeList(res?.data));
                        dispatch(enableBackToParent());
                    } else if (selectedUserId) {
                        dispatch(
                            updateTreeNode({
                                nodeId: selectedUserId,
                                children: res?.data?.children,
                            })
                        );
                    } else {
                        dispatch(setGenealogyTreeList(res?.data));
                    }
                } else {
                    if (res?.data?.code === 1085) {
                        toast.error(res?.data?.description);
                    }
                }
            },
        });
        return response;
    },

    CallSponsorTreeList: (selectedUserId = "", doubleClickedUser, userName = "") => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ["sponsor-tree-list-byid", doubleClickedUser, userName, selectedUserId],
            queryFn: () =>
                SponserTreeActions.getTreelist(
                    selectedUserId ? selectedUserId : doubleClickedUser, userName
                ),
            onSuccess: (res) => {
                if (res?.status) {
                    if (doubleClickedUser || userName) {
                        dispatch(setSponserTreeList(res?.data));
                        dispatch(enableSponserBackToParent());
                    } else if (selectedUserId) {
                        dispatch(
                            updateSponserTreeList({
                                nodeId: selectedUserId,
                                children: res?.data?.children,
                            })
                        );
                    } else {
                        dispatch(setSponserTreeList(res?.data));
                    }
                } else {
                    if (res?.data?.code === 1085) {
                        toast.error(res?.data?.description);
                    }
                }
            },
        });
        return response;
    },

    CallSponserTreeMore: (data) => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ["get-sponsor-tree-more", data],
            queryFn: () => SponserTreeService.getSponserTreeMore(data?.sponsorId, data?.position),
            onSuccess: (res) => {
                if (res.status) {
                    dispatch(
                        updateSponserTree({
                            fatherId: data?.fatherId,
                            position: data?.position,
                            newChildren: res?.data
                        })
                    )
                }
            },
            enabled: !!data?.sponsorId && !!data?.position
        });
        return response;
    },

    CallTreeViewList: (selectedUserId) => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ["tree-view-list", selectedUserId],
            queryFn: () => TreeViewActions.getTreelist(selectedUserId),
            onSuccess: (res) => {
                if (selectedUserId) {
                    dispatch(
                        updateTreeViewList({
                            nodeId: selectedUserId,
                            children: res?.data,
                        })
                    );
                } else {
                    dispatch(setTreeViewList(res?.data));
                }
            },
        });
        return response
    },

    CallUnilevelMore: (data) => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ["genealogy-unilevel-more", data],
            queryFn: () =>
                GenealogyActions.getUnilevelMore(data?.fatherId, data?.position),
            onSuccess: (res) => {
                if (res.status) {
                    dispatch(
                        updateUnilevelGenealogyTree({
                            fatherId: data?.fatherId,
                            position: data?.position,
                            newChildren: res?.data,
                        })
                    );
                }
            },
            enabled: !!data?.fatherId && !!data?.position
        });
        return response;
    },

    // -------------------------   downlinemember -------------------

    CallDownlineMembers: (level, page, itemsPerPage) => {
        const response = useQuery({
            queryKey: ["downlinemember", level, page, itemsPerPage],
            queryFn: () => downlineMembersActions.getDownlineMembers(level, page, itemsPerPage),

        })
        return response
    },
    CallDownlineHead: () => {
        const response = useQuery({
            queryKey: ["downlinehead"],
            queryFn: () => downlineMembersActions.getDownlineheaders()
        })
        return response
    },

    //---------------------referralmembers---------------

    CallReferralMembers: (level, page, itemsPerPage) => {
        const response = useQuery({
            queryKey: ["referralmembers", level, page, itemsPerPage],
            queryFn: () => ReferralMembersActions.getReferralmembers(level, page, itemsPerPage)
        })
        return response
    },
    CallReferralHead: () => {
        const response = useQuery({
            queryKey: ["referralhead"],
            queryFn: () => ReferralMembersActions.getRferralHeader()
        })
        return response
    },

    // ----------------------------------------- Register ------------------------------------------

    CallRegisterFields: () => {
        const { t } = useTranslation()
        const navigate = useNavigate()
        const response = useQuery({
            queryKey: ['get-register'],
            queryFn: RegisterFields,
            onSuccess: (res) => {
                if (res?.data?.code) {
                    toast.error(t(res?.data?.description))
                    navigate('/dashboard')
                }
            },
        })
        return response
    },
    CallRegisterFieldsCheck: () => {
        const response = useMutation((data) => RegisterFieldCheck(data.field, data.value))
        return response
    },
    CallTransPasswordCheck: (value, transPassCheck, setTransPassCheck, setSubmitButtonActive, totalAmount, transPassResposne, setTransPassResposne) => {
        const navigate = useNavigate();
        const response = useQuery({
            queryKey: ['transPass-check'],
            queryFn: () => TranssPassCheck(value, totalAmount),
            onSuccess: (data) => {
                setTransPassCheck(false);
                if (data.status === true) {
                    setTransPassResposne({
                        success: data.data
                    })
                    setSubmitButtonActive(false)
                } else if (data.code === 1014) {
                    setTransPassResposne({
                        error: data.description
                    });
                    setSubmitButtonActive(true);
                } else if (data.code === 1015) {
                    setTransPassResposne({
                        error: data.description
                    });
                    setSubmitButtonActive(true);
                }
                else {
                    toast.error(data.message)
                    navigate("/dashboard")
                }
            },
            enabled: !!(transPassCheck)
        })
        return response
    },
    CallRegisterUser: () => {
        const navigate = useNavigate()
        const { t } = useTranslation()
        const response = useMutation((registerData) => RegisterUser(registerData), {
            onSuccess: (response) => {
                if (response?.status) {
                    if (response?.data?.letterPreview === 1) {
                        navigate(`/registration-complete/${response?.data?.newUser?.username}`, {
                            replace: true,
                            state: {
                                user: response?.data?.newUser?.username,
                            },
                        });
                    } else {
                        toast.success(t("user_registered"));
                        navigate(`/dashboard`);
                    }
                } else if (response?.code) {
                    toast.error(response?.description)
                    navigate("/dashboard", { replace: true });

                } else {
                    toast.error(response?.data?.description);
                }
            },
        });
        return response;
    },
    CallRegisterLink: (linkRegisterCheck, setLinkRegisterCheck, placement = "", position = "", isRegFromTree = 0) => {
        let regFromTree = 0;
        // checking wheather reg From Tree or not
        if (isRegFromTree) {
            regFromTree = isRegFromTree;
        }
        const regFromTreePayload = {
            placement: placement,
            position: position,
            regFromTree: regFromTree
        }
        const response = useQuery({
            queryKey: ['register-link'],
            queryFn: () => CreateRegisterLink(regFromTreePayload),
            onSuccess: () => {
                setLinkRegisterCheck(false)
                localStorage.clear()
            },
            enabled: !!(linkRegisterCheck)
        })
        return response
    },
    CallStoreLink: (storeLinkCheck, setStoreLinkCheck) => {
        const response = useQuery({
            queryKey: ['store-link'],
            queryFn: () => CreateStoreLink(),
            onSuccess: () => {
                setStoreLinkCheck(false)
                localStorage.clear()
            },
            enabled: !!(storeLinkCheck)
        })
        return response
    },
    CallBankUpload: (type, username, setSubmitButtonActive, setValue, setFileResponse) => {
        const { t } = useTranslation()
        const response = useMutation((data) => BankUpload(data, username, type), {
            onSuccess: (res) => {
                if (res?.status) {
                    setSubmitButtonActive(false)
                    setFileResponse({
                        success: res?.data?.message
                    })
                    setValue('bankReceipt', res?.data?.file?.filename)
                    setSubmitButtonActive(false)
                    document.getElementById("fileUpload").value = "";
                } else {
                    if (res?.data?.code === "1017") {
                        setFileResponse({
                            error: res?.data?.description
                        })
                        setSubmitButtonActive(true)
                    } else if (res?.data?.code === "1018") {
                        setFileResponse({
                            error: res?.data?.description
                        })
                        setSubmitButtonActive(true);
                    } else {
                        setFileResponse({
                            error: t('upload_failed')
                        })
                    }
                }
            }
        })
        return response
    },
    CallDeleteBankReceipt: (setSubmitButtonActive, setValue, setFileResponse, setFile) => {
        const { t } = useTranslation()
        const response = useMutation((data) => deleteBankReceipt(data), {
            onSuccess: (res) => {
                if (res?.status) {
                    setSubmitButtonActive(true);
                    setValue('bankReceipt', undefined);
                    setFileResponse({
                        success: t(res?.data)
                    })
                    document.getElementById("fileUpload").value = "";
                }
            }
        })
        return response;
    },
    CallEwalletBalance: (getEwallet, setGetEwallet) => {
        const response = useQuery({
            queryKey: ['get-ewallet-balance'],
            queryFn: EwalletBalance,
            onSuccess: () => {
                setGetEwallet(false)
            },
            enabled: !!(getEwallet)
        })
        return response
    },
    CallLetterPreview: (username) => {
        const navigate = useNavigate()
        const response = useQuery({
            queryKey: ['letter-preview'],
            queryFn: () => LetterPreview(username),
            onSuccess: (res) => {
                if (res?.data?.code) {
                    navigate("/dashboard")
                }
            }
        })
        return response

    },
    // ----------------------------------------- Tools ------------------------------------------
    CallGetFaqs: () => {
        const response = useQuery({
            queryKey: ['get-faqs'],
            queryFn: () => getFaqs()
        })
        return response?.data?.data
    },
    CallGetNews: (callApi) => {
        const response = useQuery({
            queryKey: ['all-news'],
            queryFn: () => getNews(),
        })
        return response?.data?.data
    },
    CallGetNewsById: (newsId) => {
        const response = useQuery({
            queryKey: ['get-news-article', newsId],
            queryFn: () => getNewsById(newsId),
            enabled: !!(newsId)
        })
        return response?.data?.data
    },
    CallGetLeads: (page, itemsPerPage) => {
        const response = useQuery({
            queryKey: ['leads', page, itemsPerPage],
            queryFn: () => getLeads(page, itemsPerPage)
        })
        return response?.data?.data
    },
    CallSearchLeads: () => {
        const resposne = useMutation((searchKey) => searchLeads(searchKey));
        return resposne
    },
    CallUpdateLead: () => {
        const response = useMutation((data) => updateLead(data));
        return response;
    },
    CallGetReplicaBanner: () => {
        const response = useQuery({
            queryKey: ['get-replica-banner'],
            queryFn: () => getReplicaBanner()
        })
        return response?.data;
    },
    CallUploadReplicaBanner: () => {
        const response = useMutation((data) => uploadReplicaBanner(data));
        return response;
    },
    CallDeleteReplicaBanner: () => {
        const response = useMutation((data) => deleteReplicaBanner(data));
        return response;
    },
    CallGetDownloadMaterials: () => {
        const response = useQuery({
            queryKey: ["downloadable-material"],
            queryFn: () => getDownloadMaterials()
        })
        return response.data
    },

    // -------------------------------------------------- shopping ------------------------------------------------

    CallRepurchaseItems: () => {
        const response = useQuery({
            queryKey: ['repurchase-items'],
            queryFn: getRepurchaseItems
        })
        return response
    },
    CallAddToCart: () => {
        const response = useMutation((data) => addToCart(data))
        return response
    },
    CallCartItems: (setShowCartItems = null) => {
        const response = useQuery({
            queryKey: ['cart-items'],
            queryFn: getCartItems,
            onSuccess: (res) => {
                if (setShowCartItems) {
                    setShowCartItems(true)
                }
            }
        })
        return response
    },
    CallDecrementCartItem: () => {
        const response = useMutation((data) => decrementCartItem(data))
        return response
    },
    CallRemoveCartItem: () => {
        const response = useMutation((data) => removeCartItem(data))
        return response
    },
    CallAddAddress: () => {
        const response = useMutation((data) => addAddress(data))
        return response
    },
    CallGetAddress: () => {
        const response = useQuery({
            queryKey: ['get-address'],
            queryFn: getAddress
        })
        return response
    },
    CallPaymentMethods: (action) => {
        const response = useQuery({
            queryKey: ['payment-methods'],
            queryFn: () => callPaymentMethods(action)
        })
        return response
    },
    CallRemoveAddress: () => {
        const response = useMutation((data) => removeAddress(data))
        return response
    },
    CallProductDetails: (id) => {
        const response = useQuery({
            queryKey: ['product-details'],
            queryFn: () => ProductDetails(id)
        })
        return response
    },
    CallDefaultAddressChange: () => {
        const response = useMutation((id) => DefaultAddressChange(id))
        return response
    },
    CallPlaceRepurchaseOrder: () => {
        const response = useMutation((data) => PlaceRepurchaseOrder(data))
        return response
    },
    CallRepurchaseReport: (page, limit) => {
        const response = useQuery({
            queryKey: ['repurchase-report', page, limit],
            queryFn: () => RepurchaseReport(page, limit)
        })
        return response
    },
    CallRepurchaseInvoice: (orderId) => {
        const response = useQuery({
            queryKey: ['repurchase-invoice'],
            queryFn: () => RepurchaseInvoice(orderId)
        })
        return response
    },
    // -------------------------------------------------- Lcp ------------------------------------------------
    CallGetReplicaApi: (adminUsername) => {
        const response = useQuery({
            queryKey: ['get-api-key'],
            queryFn: () => getReplicaApi(adminUsername),
            onSuccess: (res) => {
                localStorage.setItem("apiKey", res?.apiKey);
            }
        })
        return response.data
    },
    CallGetCompanyDetails: (referraiId, hash) => {
        const response = useQuery({
            queryKey: ['get-company-details'],
            queryFn: () => getCompanyDetails(referraiId, hash),
            onSuccess: (res) => {
                if (res?.status) {
                    if (res?.data?.defaultLang !== null) {
                        localStorage.setItem("userLanguage", JSON.stringify({ code: res?.data?.defaultLang.code }));
                    } else {
                        localStorage.setItem("userLanguage", JSON.stringify({ code: "en" }));
                    }
                }
            },
            enabled: !!(localStorage.getItem("apiKey"))
        })
        return response?.data?.data
    },
    CallAddLcpLead: () => {
        const response = useMutation((body) => AddLcpLead(body))
        return response
    },

    //  ------------------------------------------------- Replica ---------------------------------------------------------

    CallReplicaApiKey: (adminUsername, username, hashKey) => {
        const navigate = useNavigate()
        const response = useQuery({
            queryKey: ['replica-api-key'],
            queryFn: () => getApiKey(adminUsername),
            onSuccess: (res) => {
                if (res.status) {
                    if (username || hashKey) {
                        localStorage.setItem('referralId', username)
                        localStorage.setItem('hashKey', hashKey)
                        localStorage.setItem('apiKey', res?.data?.apiKey)
                        localStorage.setItem('admin_user_name', adminUsername)
                    }
                } else {
                    if (res?.data?.code) {
                        toast.error(res?.data?.description)
                    }
                    navigate('/login')
                }
            },
        })
        return response
    },
    CallReplicaHome: () => {
        const { i18n } = useTranslation()
        const navigate = useNavigate()
        const dispatch = useDispatch()
        const response = useQuery({
            queryKey: ['replica-home'],
            queryFn: ReplicaHome,
            onSuccess: (res) => {
                if (res?.status) {
                    dispatch(setTermsAndPolicy(res?.data?.replicaHome));
                    dispatch(setCompanyDetails(res?.data?.companyDetails));
                    dispatch(setRegisterLink(res?.data?.registrationUrl));
                    localStorage.setItem("userLanguage", JSON.stringify({ code: res?.data?.langId }))
                    i18n.changeLanguage(res?.data?.langId)
                } else {
                    if (res?.data?.code) {
                        toast.error(res?.data?.description)
                    }
                    navigate('/login')
                }
            }
        })
        return response
    },
    CallReplicaRegisterFields: () => {
        const navigate = useNavigate()
        const dispatch = useDispatch()
        const response = useQuery({
            queryKey: ['replica-register-fields'],
            queryFn: ReplicaRegisterFields,
            onSuccess: (res) => {
                if (res?.status) {
                    dispatch(setTermsAndPolicy(res?.data?.replicaTerms))
                    dispatch(setSelectedCurrency(res?.data?.user?.selectedCurrency ?? res?.data?.user?.defaultCurrency));
                    dispatch(
                        setConversionFactors({
                            currencies: res?.data?.currencies,
                            selectedCurrency: res?.data?.user?.selectedCurrency ?? res?.data?.user?.defaultCurrency,
                            defaultCurrency: res?.data?.user?.defaultCurrency
                        }))
                } else {
                    localStorage.clear()
                    navigate('/login')
                }
            }
        })
        return response
    },
    CallReplicaFieldCheck: () => {
        const response = useMutation((data) => replicaFieldCheck(data.field, data.value))
        return response
    },
    CallReplicaBankRecieptUpload: (type, username, referralId, setSubmitButtonActive, setValue, setFileResponse) => {
        const { t } = useTranslation()
        const response = useMutation((data) => ReplicaBankUploadReceipt(data, username, referralId, type), {
            onSuccess: (res) => {
                setSubmitButtonActive(false)
                if (res?.status) {
                    setFileResponse({
                        success: res?.data?.message
                    })
                    setValue('bankReceipt', res?.data?.file?.filename)
                    setSubmitButtonActive(false)
                    document.getElementById("bankReciept").value = "";
                } else {
                    if (res?.data?.code === "1017") {
                        setFileResponse({
                            error: res?.data?.description
                        })
                        setSubmitButtonActive(true)
                    } else if (res?.data?.code === "1018") {
                        setFileResponse({
                            error: res?.data?.description
                        })
                        setSubmitButtonActive(true)
                    } else {
                        setFileResponse({
                            error: t('upload_failed')
                        })
                    }
                }
            }
        })
        return response
    },
    CallReplicaBankRecieptDelete: (setSubmitButtonActive, setValue, setFileResponse, setFile) => {
        const { t } = useTranslation()
        const response = useMutation((data) => ReplicaBankRecieptDelete(data), {
            onSuccess: (res) => {
                if (res?.status) {
                    setSubmitButtonActive(true);
                    setValue('bankReceipt', undefined);
                    setFileResponse({
                        success: t(res?.data)
                    })
                    document.getElementById("bankReciept").value = "";
                    setFile(null);
                }
            }
        })
        return response
    },
    CallReplicaRegisterPost: () => {
        const navigate = useNavigate()
        const { t } = useTranslation()
        const hash = localStorage.getItem('hashKey')
        const referraiId = localStorage.getItem('referralId')
        const response = useMutation((data) => ReplicaRegisterPost(data), {
            onSuccess: (res) => {
                if (res.status) {
                    toast.success(t("user_registered"))
                    navigate(`/replica/${referraiId}/${hash}`)
                } else if (res?.data?.code === 1009) {
                    toast.error(res?.data?.description)
                    navigate(`/replica/${referraiId}/${hash}`)

                } else {
                    toast.error(res?.description);
                }
            }
        })
        return response
    },
    CallReplicaContactUpload: () => {
        const response = useMutation((data) => ReplicaContactUpload(data))
        return response
    },
    //  ------------------------------------------------- Upgrade & Renewal ---------------------------------------------------------
    CallGetUpgradeProducts: () => {
        const response = useQuery({
            queryKey: ['get-upgrade-products'],
            queryFn: UpgradeActions.getUpgradeProducts
        })
        return response?.data
    },
    CallUpgradeSubscription: () => {
        const { t } = useTranslation();
        const navigate = useNavigate();
        const response = useMutation((upgradeData) => UpgradeActions.upgradeSubscription(upgradeData), {
            onSuccess: (res) => {
                if (res?.status) {
                    toast.success(t(res?.data));
                    navigate('/profile');
                } else {
                    if (res?.data?.code) {
                        toast.error(t(res?.data?.description));
                    }
                }
            }
        })
        return response
    },
    CallGetSubscriptionDetails: () => {
        const response = useQuery({
            queryKey: ['get-subscription-details'],
            queryFn: RenewActions.getUpgradeProducts
        })
        return response?.data
    },
    CallRenewSubscription: () => {
        const response = useMutation((renewData) => RenewActions.renewSubscription(renewData))
        return response
    },
    CallAutoSubscription: () => {
        const response = useMutation((data) => RenewActions.AutoSubscription(data))
        return response
    },
    CallCancelSubscription: () => {
        const response = useMutation((data) => RenewActions.CancelSubscription(data))
        return response
    },

    // ----------------------------------- Mailbox -------------------------------------------------
    CallInboxes: (page, limit, selectedPageCheck) => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ['inbox', page],
            queryFn: () => Inboxes(page, limit),
            onSuccess: (res) => {
                if (res.status) {
                    const inboxData = res?.data?.data;
                    if (inboxData) {
                        if (page === 1) {
                            dispatch(
                                setMails(inboxData)
                            );
                        } else {
                            dispatch(
                                addMail(inboxData)
                            )
                        }
                    }
                }
            },
            enabled: !!selectedPageCheck.inbox
        })
        return response
    },
    CallSingleMailDetails: (data, mailCheck, setMailCheck, type) => {
        const response = useQuery({
            queryKey: ['single-mail-details'],
            queryFn: () => SingleMail(data, type),
            onSuccess: () => {
                setMailCheck(false)
            },
            enabled: !!mailCheck
        })
        return response
    },
    CallReplyMail: () => {
        const response = useMutation((replyMail) => ReplyMail(replyMail))
        return response
    },
    CallAdminInbox: (page, limit, selectedPageCheck) => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ['inbox-from-admin', page],
            queryFn: () => AdminInboxes(page, limit),
            onSuccess: (res) => {
                if (res.status) {
                    if (page === 1) {
                        dispatch(
                            setMails(res?.data?.data)
                        );
                    } else {
                        dispatch(
                            addMail(res?.data?.data)
                        )
                    }
                }
            },
            enabled: !!selectedPageCheck.adminInbox
        })
        return response
    },
    CallSendInternalMail: () => {
        const mutation = useMutation((mailContent) => SendInternalMail(mailContent))
        return mutation
    },
    CallDeleteMail: () => {
        const mutation = useMutation((mailId) => DeleteMail(mailId))
        return mutation
    },
    CallSentMail: (page, limit, selectedPageCheck) => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ['sent', page],
            queryFn: () => SentMail(page, limit),
            onSuccess: (res) => {
                if (res.status) {
                    if (page === 1) {
                        dispatch(
                            setMails(res?.data?.data)
                        );
                    } else {
                        dispatch(
                            addMail(res?.data?.data)
                        )
                    }
                }
            },
            enabled: !!selectedPageCheck.sent
        })
        return response
    },
    CallReplicaInbox: (page, limit, selectedPageCheck) => {
        const dispatch = useDispatch();
        const response = useQuery({
            queryKey: ['replicaInbox', page],
            queryFn: () => replicaInbox(page, limit),
            onSuccess: (res) => {
                if (res.status) {
                    if (page === 1) {
                        dispatch(
                            setMails(res?.data?.data)
                        );
                    } else {
                        dispatch(
                            addMail(res?.data?.data)
                        )
                    }
                }
            },
            enabled: !!selectedPageCheck.replicaInbox
        })
        return response
    },

    //------------------------------------ Tickets ---------------------------------------

    CallTickets: (page, itemsPerPage, category, priority, ticketId, status) => {
        const response = useQuery({
            queryKey: ["tickets", page, itemsPerPage, category, priority, ticketId, status],
            queryFn: () => getTickets(page, itemsPerPage, category, priority, ticketId, status),
        });
        return response
    },
    CallTicketPartials: () => {
        const response = useQuery({
            queryKey: ['ticket-partials'],
            queryFn: getTicketPartials
        })
        return response
    },
    CallTrackId: () => {
        const response = useQuery({
            queryKey: ['get-trackId'],
            queryFn: getTrackId
        })
        return response
    },
    CallCreateTicket: () => {
        const response = useMutation((data) => createTicket(data))
        return response
    },
    CallTicketDetails: (trackId) => {
        const response = useQuery({
            queryKey: ['ticket-details'],
            queryFn: () => getTicketDetails(trackId)
        })
        return response
    },
    CallTicketReplies: (trackId) => {
        const response = useQuery({
            queryKey: ['ticket-replies'],
            queryFn: () => getTicketReplies(trackId)
        })
        return response
    },
    CallTicketReply: () => {
        const response = useMutation((data) => ticketReply(data))
        return response
    },
    CallTicketTimeline: (trackId) => {
        const response = useQuery({
            queryKey: ['ticket-timeline'],
            queryFn: () => ticketTimeline(trackId)
        })
        return response
    },
    CallTicketFaqs: () => {
        const response = useQuery({
            queryKey: ['ticket-faqs'],
            queryFn: getTicketFaqs
        })
        return response
    },

    // ------------------------------------------------------ CRM ----------------------------------------------------

    CallCrmTiles: () => {
        const response = useQuery({
            queryKey: ['crm-tiles'],
            queryFn: CrmTiles
        })
        return response
    },
    CallCrmGraph: () => {
        const response = useQuery({
            queryKey: ['crm-graph'],
            queryFn: CrmGraph
        })
        return response
    },
    CallFollowupToday: (page, itemsPerPage) => {
        const response = useQuery({
            queryKey: ['followup-today', page, itemsPerPage],
            queryFn: () => FollowupToday(page, itemsPerPage)
        })
        return response
    },
    CallRecentLeads: (page, itemsPerPage) => {
        const response = useQuery({
            queryKey: ['recent-leads', page, itemsPerPage],
            queryFn: () => RecentLeads(page, itemsPerPage)
        })
        return response
    },
    CallMissedFollowup: (page, itemsPerPage) => {
        const response = useQuery({
            queryKey: ['missed-followup', page, itemsPerPage],
            queryFn: () => MissedFollowup(page, itemsPerPage)
        })
        return response
    },
    CallViewLeads: (data, apiCheck, setApiCheck, page, itemsPerPage) => {
        const response = useQuery({
            queryKey: ['view-leads', page, itemsPerPage, apiCheck],
            queryFn: () => ViewLeads(data, page, itemsPerPage),
        })
        return response
    },
    CallEditCrmLead: () => {
        const response = useMutation((data) => EditCrmLead(data));
        return response;
    },
    CallAddFollowUp: () => {
        const response = useMutation((data) => AddFollowUp(data));
        return response;
    },
    CallCrmTimeLine: (data) => {
        const response = useQuery({
            queryKey: ['crm-timeline'],
            queryFn: () => crmTimeline(data),
            enabled: !!data
        })
        return response
    },
    CallAddCrmLead: () => {
        const response = useMutation((data) => AddCrmLead(data))
        return response
    },
    CallGetCountries: () => {
        const response = useQuery({
            queryKey: ['get-countries'],
            queryFn: GetCountries
        })
        return response
    },
    CallLeadDetails: (id) => {
        const response = useQuery({
            queryKey: ['lead-details'],
            queryFn: () => LeadDetails(id)
        })
        return response
    },
    CallAddNextFollowUp: () => {
        const response = useMutation((data) => addNextFollowUp(data));
        return response;
    },
    //stripe 
    CallPaymentIntent: () => {
        const response = useMutation((data) => CreatePaymentIntent(data))
        return response
    },

    CallNowPaymentIntent: () => {
        const response = useMutation((paymentId) => GetPaymentGatewayKey(paymentId))
        return response
    },
};
