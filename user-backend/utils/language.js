import Language from '../models/language.js';

export default async () => await Language.findOne({ where : {status:1, default: 1}});