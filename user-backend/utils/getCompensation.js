import Compensation from '../models/compensation.js'

export default async () => await Compensation.findOne({ raw: true});