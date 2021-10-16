import { Model } from '@vuex-orm/core'
import Slot from './Slot'
export default class EntryPoint extends Model {
  // This is the name used as module name of the Vuex Store.
  static entity = 'entryPoints'

  // List of all fields (schema) of the post model. `this.attr` is used
  // for the generic field type. The argument is the default value.
  static fields () {
    return {
      id: this.attr(null),
      name: this.attr(null),
      slots: this.hasMany(Slot, 'entry_point_id')
    }
  }
}
