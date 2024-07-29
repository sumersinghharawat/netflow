import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import enCommon from '../src/locales/en/en_common.json';
import enEwallet from '../src/locales/en/en_ewallet.json';
import enDashboard from '../src/locales/en/en_dashboard.json'
import enProfile from '../src/locales/en/en_profile.json'
import enPayout from '../src/locales/en/en_payout.json'
import enEpin from '../src/locales/en/en_epin.json'
import enRegister from '../src/locales/en/en_register.json'
import enTree from '../src/locales/en/en_tree.json'
import enError from '../src/locales/en/en_error.json'
import enShopping from '../src/locales/en/en_shopping.json'
import enReplica from '../src/locales/en/en_replica.json'
import enMailbox from '../src/locales/en/en_mailBox.json'
import enLeads from '../src/locales/en/en_leads.json'
import enSubscription from '../src/locales/en/en_subscription.json'
import enTicket from '../src/locales/en/en_tickets.json'
import enCrm from '../src/locales/en/en_crm.json'
import esCommon from '../src/locales/es/es_common.json'
import esDashboard from '../src/locales/es/es_dashboard.json'
import esProfile from '../src/locales/es/es_profile.json'
import esEwallet from '../src/locales/es/es_ewallet.json'
import esEpin from '../src/locales/es/es_epin.json'
import esRegister from '../src/locales/es/es_register.json'
import esTree from '../src/locales/es/es_tree.json'
import esError from '../src/locales/es/es_error.json'
import esPayout from '../src/locales/es/es_payout.json'
import esShopping from '../src/locales/es/es_shopping.json'
import esLeads from '../src/locales/es/es_leads.json'
import esSubscription from '../src/locales/es/es_subscription.json'
import esReplica from '../src/locales/es/es_replica.json'
import esMailbox from '../src/locales/es/es_mailBox.json'
import esTicket from '../src/locales/es/es_tickets.json'
import arCommon from '../src/locales/ar/ar_common.json'
import arDashboard from '../src/locales/ar/ar_dashboard.json'
import arProfile from '../src/locales/ar/ar_profile.json'
import arEwallet from '../src/locales/ar/ar_ewallet.json'
import arEpin from '../src/locales/ar/ar_epin.json'
import arRegister from '../src/locales/ar/ar_register.json'
import arTree from '../src/locales/ar/ar_tree.json'
import arPayout from '../src/locales/ar/ar_payout.json'
import arError from '../src/locales/ar/ar_error.json'
import arShopping from '../src/locales/ar/ar_shopping.json'
import arLeads from '../src/locales/ar/ar_leads.json'
import arSubscription from '../src/locales/ar/ar_subscription.json'
import arReplica from '../src/locales/ar/ar_replica.json'
import arMailbox from '../src/locales/ar/ar_mailBox.json'
import arTicket from '../src/locales/ar/ar_tickets.json'
import chCommon from '../src/locales/ch/ch_common.json'
import chDashboard from '../src/locales/ch/ch_dashboard.json'
import chProfile from '../src/locales/ch/ch_profile.json'
import chEwallet from '../src/locales/ch/ch_ewallet.json'
import chEpin from '../src/locales/ch/ch_epin.json'
import chRegister from '../src/locales/ch/ch_register.json'
import chTree from '../src/locales/ch/ch_tree.json'
import chPayout from '../src/locales/ch/ch_payout.json'
import chError from '../src/locales/ch/ch_error.json'
import chShopping from '../src/locales/ch/ch_shopping.json'
import chLeads from '../src/locales/ch/ch_leads.json'
import chSubscription from '../src/locales/ch/ch_subscription.json'
import chReplica from '../src/locales/ch/ch_replica.json'
import chMailbox from '../src/locales/ch/ch_mailBox.json'
import chTicket from '../src/locales/ch/ch_tickets.json'
import deCommon from '../src/locales/de/de_common.json'
import deDashboard from '../src/locales/de/de_dashboard.json'
import deProfile from '../src/locales/de/de_profile.json'
import deEwallet from '../src/locales/de/de_ewallet.json'
import deEpin from '../src/locales/de/de_epin.json'
import deRegister from '../src/locales/de/de_register.json'
import deTree from '../src/locales/de/de_tree.json'
import dePayout from '../src/locales/de/de_payout.json'
import deError from '../src/locales/de/de_error.json'
import deShopping from '../src/locales/de/de_shopping.json'
import deLeads from '../src/locales/de/de_leads.json'
import deSubscription from '../src/locales/de/de_subscription.json'
import deReplica from '../src/locales/de/de_replica.json'
import deMailbox from '../src/locales/de/de_mailBox.json'
import deTicket from '../src/locales/de/de_tickets.json'
import frCommon from '../src/locales/fr/fr_common.json'
import frDashboard from '../src/locales/fr/fr_dashboard.json'
import frProfile from '../src/locales/fr/fr_profile.json'
import frEwallet from '../src/locales/fr/fr_ewallet.json'
import frEpin from '../src/locales/fr/fr_epin.json'
import frRegister from '../src/locales/fr/fr_register.json'
import frTree from '../src/locales/fr/fr_tree.json'
import frPayout from '../src/locales/fr/fr_payout.json'
import frError from '../src/locales/fr/fr_error.json'
import frShopping from '../src/locales/fr/fr_shopping.json'
import frLeads from '../src/locales/fr/fr_leads.json'
import frSubscription from '../src/locales/fr/fr_subscription.json'
import frReplica from '../src/locales/fr/fr_replica.json'
import frMailbox from '../src/locales/fr/fr_mailBox.json'
import frTicket from '../src/locales/fr/fr_tickets.json'
import itCommon from '../src/locales/it/it_common.json'
import itDashboard from '../src/locales/it/it_dashboard.json'
import itProfile from '../src/locales/it/it_profile.json'
import itEwallet from '../src/locales/it/it_ewallet.json'
import itEpin from '../src/locales/it/it_epin.json'
import itRegister from '../src/locales/it/it_register.json'
import itTree from '../src/locales/it/it_tree.json'
import itPayout from '../src/locales/it/it_payout.json'
import itError from '../src/locales/it/it_error.json'
import itShopping from '../src/locales/it/it_shopping.json'
import itLeads from '../src/locales/it/it_leads.json'
import itSubscription from '../src/locales/it/it_subscription.json'
import itReplica from '../src/locales/it/it_replica.json'
import itMailbox from '../src/locales/it/it_mailBox.json'
import itTicket from '../src/locales/it/it_tickets.json'
import poCommon from '../src/locales/po/po_common.json'
import poDashboard from '../src/locales/po/po_dashboard.json'
import poProfile from '../src/locales/po/po_profile.json'
import poEwallet from '../src/locales/po/po_ewallet.json'
import poEpin from '../src/locales/po/po_epin.json'
import poRegister from '../src/locales/po/po_register.json'
import poTree from '../src/locales/po/po_tree.json'
import poPayout from '../src/locales/po/po_payout.json'
import poError from '../src/locales/po/po_error.json'
import poShopping from '../src/locales/po/po_shopping.json'
import poLeads from '../src/locales/po/po_leads.json'
import poSubscription from '../src/locales/po/po_subscription.json'
import poReplica from '../src/locales/po/po_replica.json'
import poMailbox from '../src/locales/po/po_mailBox.json'
import poTicket from '../src/locales/po/po_tickets.json'
import ptCommon from '../src/locales/pt/pt_common.json'
import ptDashboard from '../src/locales/pt/pt_dashboard.json'
import ptProfile from '../src/locales/pt/pt_profile.json'
import ptEwallet from '../src/locales/pt/pt_ewallet.json'
import ptEpin from '../src/locales/pt/pt_epin.json'
import ptRegister from '../src/locales/pt/pt_register.json'
import ptTree from '../src/locales/pt/pt_tree.json'
import ptPayout from '../src/locales/pt/pt_payout.json'
import ptError from '../src/locales/pt/pt_error.json'
import ptShopping from '../src/locales/pt/pt_shopping.json'
import ptLeads from '../src/locales/pt/pt_leads.json'
import ptSubscription from '../src/locales/pt/pt_subscription.json'
import ptReplica from '../src/locales/pt/pt_replica.json'
import ptMailbox from '../src/locales/pt/pt_mailBox.json'
import ptTicket from '../src/locales/pt/pt_tickets.json'
import ruCommon from '../src/locales/ru/ru_common.json'
import ruDashboard from '../src/locales/ru/ru_dashboard.json'
import ruProfile from '../src/locales/ru/ru_profile.json'
import ruEwallet from '../src/locales/ru/ru_ewallet.json'
import ruEpin from '../src/locales/ru/ru_epin.json'
import ruRegister from '../src/locales/ru/ru_register.json'
import ruTree from '../src/locales/ru/ru_tree.json'
import ruPayout from '../src/locales/ru/ru_payout.json'
import ruError from '../src/locales/ru/ru_error.json'
import ruShopping from '../src/locales/ru/ru_shopping.json'
import ruLeads from '../src/locales/ru/ru_leads.json'
import ruSubscription from '../src/locales/ru/ru_subscription.json'
import ruReplica from '../src/locales/ru/ru_replica.json'
import ruMailbox from '../src/locales/ru/ru_mailBox.json'
import ruTicket from '../src/locales/ru/ru_tickets.json'
import trCommon from '../src/locales/tr/tr_common.json'
import trDashboard from '../src/locales/tr/tr_dashboard.json'
import trProfile from '../src/locales/tr/tr_profile.json'
import trEwallet from '../src/locales/tr/tr_ewallet.json'
import trEpin from '../src/locales/tr/tr_epin.json'
import trRegister from '../src/locales/tr/tr_register.json'
import trTree from '../src/locales/tr/tr_tree.json'
import trPayout from '../src/locales/tr/tr_payout.json'
import trError from '../src/locales/tr/tr_error.json'
import trShopping from '../src/locales/tr/tr_shopping.json'
import trLeads from '../src/locales/tr/tr_leads.json'
import trSubscription from '../src/locales/tr/tr_subscription.json'
import trReplica from '../src/locales/tr/tr_replica.json'
import trMailbox from '../src/locales/tr/tr_mailBox.json'
import trTicket from '../src/locales/tr/tr_tickets.json'

const savedLanguage = JSON.parse(localStorage.getItem('userLanguage'));

const mergedTranslations = {
  en: {
    translation: { ...enCommon, ...enEwallet, ...enDashboard, ...enPayout, ...enEpin, ...enProfile, ...enRegister, ...enTree, ...enError, ...enShopping, ...enReplica, ...enLeads, ...enMailbox, ...enSubscription, ...enTicket, ...enCrm },
  },
  es: {
    translation: { ...esCommon, ...esDashboard, ...esProfile, ...esEwallet, ...esEpin, ...esRegister, ...esTree, ...esPayout, ...esError, ...esShopping, ...esReplica, ...esLeads, ...esMailbox, ...esSubscription, ...esTicket }
  },
  ar: {
    translation: { ...arCommon, ...arDashboard, ...arProfile, ...arEwallet, ...arEpin, ...arRegister, ...arTree, ...arPayout, ...arError, ...arShopping, ...arReplica, ...arLeads, ...arMailbox, ...arSubscription, ...arTicket }
  },
  ch: {
    translation: { ...chCommon, ...chDashboard, ...chProfile, ...chEwallet, ...chEpin, ...chRegister, ...chTree, ...chPayout, ...chError, ...chShopping, ...chReplica, ...chLeads, ...chMailbox, ...chSubscription, ...chTicket }
  },
  de: {
    translation: { ...deCommon, ...deDashboard, ...deProfile, ...deEwallet, ...deEpin, ...deRegister, ...deTree, ...dePayout, ...deError, ...deShopping, ...deReplica, ...deLeads, ...deMailbox, ...deSubscription, ...deTicket }
  },
  fr: {
    translation: { ...frCommon, ...frDashboard, ...frProfile, ...frEwallet, ...frEpin, ...frRegister, ...frTree, ...frPayout, ...frError, ...frShopping, ...frReplica, ...frLeads, ...frMailbox, ...frSubscription, ...frTicket }
  },
  it: {
    translation: { ...itCommon, ...itDashboard, ...itProfile, ...itEwallet, ...itEpin, ...itRegister, ...itTree, ...itPayout, ...itError, ...itShopping, ...itReplica, ...itLeads, ...itMailbox, ...itSubscription, ...itTicket }
  },
  po: {
    translation: { ...poCommon, ...poDashboard, ...poProfile, ...poEwallet, ...poEpin, ...poRegister, ...poTree, ...poPayout, ...poError, ...poShopping, ...poReplica, ...poLeads, ...poMailbox, ...poSubscription, ...poTicket }
  },
  pt: {
    translation: { ...ptCommon, ...ptDashboard, ...ptProfile, ...ptEwallet, ...ptEpin, ...ptRegister, ...ptTree, ...ptPayout, ...ptError, ...ptShopping, ...ptReplica, ...ptLeads, ...ptMailbox, ...ptSubscription, ...ptTicket }
  },
  ru: {
    translation: { ...ruCommon, ...ruDashboard, ...ruProfile, ...ruEwallet, ...ruEpin, ...ruRegister, ...ruTree, ...ruPayout, ...ruError, ...ruShopping, ...ruReplica, ...ruLeads, ...ruMailbox, ...ruSubscription, ...ruTicket }
  },
  tr: {
    translation: { ...trCommon, ...trDashboard, ...trProfile, ...trEwallet, ...trEpin, ...trRegister, ...trTree, ...trPayout, ...trError, ...trShopping, ...trReplica, ...trLeads, ...trMailbox, ...trSubscription, ...trSubscription, ...trTicket }
  }


};

i18n
  .use(initReactI18next)
  .init({
    resources: mergedTranslations,
    lng: (savedLanguage?.code) ? savedLanguage?.code : 'en',
    interpolation: {
      escapeValue: false,
    },
  });


export default i18n;
