const amountTypes = {
    commissions: ["referral",
        "leg",
        "repurchase_leg",
        "upgrade_leg",
        "rank_bonus",
        "daily_investment",
        "level_commission",
        "repurchase_level_commission",
        "upgrade_level_commission",
        "xup_commission",
        "xup_repurchase_level_commission",
        "xup_upgrade_level_commission",
        "pool_bonus",
        "fast_start_bonus",
        "matching_bonus",
        "matching_bonus_purchase",
        "matching_bonus_upgrade",
        "sales_commission",
        "stair_step",
        "override_bonus",
        "board_commission",
        "donation",
        "purchase_donation"
    ],
    pin_status: [
        "pin_purchase",
        "pin_purchase_refund",
        "pin_purchase_delete"
    ],
    amount_types: [
        "admin_credit",
        "admin_debit",
        "admin_user_credit",
        "admin_user_debit",
        "user_credit",
        "user_debit",
        "payout_request",
        "payout_fee",
        "payout_release_manual",
        "payout_delete",
        "payout_inactive",
        "withdrawal_cancel",
    ],

}

const ewalletText = {
    "admin_credit": "credited_by",
    "admin_debit": "debited_by",
    "user_credit": "fund_transfer_from",
    "admin_user_credit": "fund_transfer_from",
    "donation": {
        "debit": "donation_debit",
        "credit": "donation_credit"
    },
    "board_commission": "table_commission",
    "ewallet_payment": {
        "registration": "deducted_for_registration_of",
        "repurchase": "deducted_for_repurchase_by",
        "package_validity": "deducted_for_membership_renewal_of",
        "upgrade": "deducted_for_upgrade_of"
    },
    "payout": {
        "payout_request": "deducted_for_payout_request",
        "payout_inactive": "payout_inactive",
        "payout_release": "payout_release_for_request",
        "payout_delete": "credited_for_payout_request_delete",
        "payout_release_manual": "payout_released_by_manual",
        "withdrawal_cancel": "credited_for_waiting_withdrawal_cancel"
    },
    "package_purchase": { "purchase_donation": "purchase_donation_from" },
    "pin_purchase": {
        "pin_purchase": "deducted_for_pin_purchase",
        "pin_purchase_refund": "credited_for_pin_purchase_refund",
        "pin_purchase_delete": "credited_for_pin_purchase_delete"
    }
}