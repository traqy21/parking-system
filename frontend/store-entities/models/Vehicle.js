import { Model } from '@vuex-orm/core'
export default class Vehicle extends Model {
  // This is the name used as module name of the Vuex Store.
  static entity = 'vehicles'

  // List of all fields (schema) of the post model. `this.attr` is used
  // for the generic field type. The argument is the default value.
  static fields () {
    return {
      id: this.attr(null),
      plate_no: this.attr(null),
      type: this.attr(null)
    }
  }
}
