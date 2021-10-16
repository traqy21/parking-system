import { Model } from '@vuex-orm/core'
import Slot from './Slot'
import Vehicle from './Vehicle'
export default class ParkingTransaction extends Model {
  // This is the name used as module name of the Vuex Store.
  static entity = 'parkingTransactions'

  // List of all fields (schema) of the post model. `this.attr` is used
  // for the generic field type. The argument is the default value.
  static fields () {
    return {
      id: this.attr(null),
      reference: this.attr(null),
      vehicle_id: this.attr(null),
      slot_id: this.attr(null),
      status: this.attr(null),
      exit_time: this.attr(null),
      rate: this.attr(null),
      description: this.attr(null),
      created_at: this.attr(null),
      slot: this.belongsTo(Slot, 'slot_id'),
      vehicle: this.belongsTo(Vehicle, 'vehicle_id')
    }
  }
}
