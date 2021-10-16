import { Model } from '@vuex-orm/core'
export default class Slot extends Model {
  // This is the name used as module name of the Vuex Store.
  static entity = 'slots'

  // List of all fields (schema) of the post model. `this.attr` is used
  // for the generic field type. The argument is the default value.
  static fields () {
    return {
      id: this.attr(null),
      entry_point_id: this.attr(null),
      size: this.attr(null),
      slot_no: this.attr(null),
      distance: this.attr(null),
      exceeding_hourly_rate: this.attr(null),
      is_vacant: this.attr(null)
    }
  }
}
