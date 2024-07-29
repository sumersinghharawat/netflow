import TermsAndCondition from "../models/termsAndCondition.js";



export default async (langId) => await TermsAndCondition.findOne({ attributes: ['id', 'termsAndConditions'], where: { languageId: langId ?? 1 }, raw: true });