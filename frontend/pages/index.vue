<template>
  <v-row>
    <v-col cols="8">
      <v-card class="ma-3 pa-3">
        <div class="d-flex flex-row">
          <v-card-title>PARKING COMPLEX</v-card-title>
          <v-btn
            color="primary"
            rounded
            medium
            depressed
            class="text-none ma-2"
            @click="redirectTransactions()"
          >
            View transactions
          </v-btn>
        </div>
        <v-row v-for="(item, key) in entryPoints" :key="key">
          <v-col cols="12">
            <h3>{{ item.name }} - Slots</h3>
          </v-col>
          <v-col cols="12">
            <v-row>
              <v-col v-for="(slotItem, skey) in item.slots" :key="skey" cols="4">
                <slot-card :parking-slot="slotItem" />
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-card>
    </v-col>
    <v-col cols="4">
      <div class="d-flex flex-column">
        <v-card class="pa-3 ma-3">
          <div class="d-flex flex-column">
            <div class="d-flex flex-row">
              <v-text-field
                v-model="date"
                outlined
                placeholder="Date"
                type="date"
              />

              <v-text-field
                v-model="time"
                outlined
                placeholder="Time"
                type="time"
              />
            </div>
            <v-switch
              v-model="use_server_time"
              label="Use server time"
              @change="updateDate()"
            />
          </div>

          <ValidationObserver
            ref="refParkObserver"
          >
            <div class="d-flex flex-column pa-3">
              <h3 class="pb-2">
                Park vehicle:
              </h3>
              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="vehicle.plate_no"
              >
                <v-text-field
                  v-model="vehicle.plate_no"
                  outlined
                  placeholder="Plate No."
                  :error-messages="errors"
                />
              </ValidationProvider>

              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="vehicle.type"
              >
                <v-select
                  v-model="vehicle.type"
                  :items="vehicleTypes"
                  item-text="name"
                  item-value="id"
                  outlined
                  label="Vehicle Type"
                  :error-messages="errors"
                >
                  <template v-slot:selection="{ item }">
                    <span class="text-md-left" style="width: 100%;">
                      {{ item.name }}
                    </span>
                  </template>
                </v-select>
              </ValidationProvider>

              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="entrypoint"
              >
                <v-select
                  v-model="entry_point_id"
                  :items="entryPoints"
                  item-text="name"
                  item-value="id"
                  outlined
                  label="Entry points"
                  :error-messages="errors"
                >
                  <template v-slot:selection="{ item }">
                    <span class="text-md-left" style="width: 100%;">
                      {{ item.name }}
                    </span>
                  </template>
                </v-select>
              </ValidationProvider>

              <v-btn
                color="primary"
                rounded
                large
                depressed
                class="text-none ma-2"
                @click="parkVehicle()"
              >
                Park
              </v-btn>
            </div>
          </ValidationObserver>
        </v-card>

        <v-card class="pa-3 ma-3">
          <div class="d-flex flex-column pa-3">
            <ValidationObserver
              ref="refUnparkParkObserver"
            >
              <h3 class="pb-2">
                Unpark vehicle:
              </h3>

              <ValidationProvider
                v-slot="{ errors }"
                rules="required"
                name="reference"
              >
                <v-text-field
                  v-model="reference"
                  outlined
                  placeholder="Reference ID"
                  :error-messages="errors"
                />
              </ValidationProvider>
            </ValidationObserver>

            <v-btn
              color="primary"
              rounded
              large
              depressed
              class="text-none ma-2"
              @click="unparkVehicle()"
            >
              Unpark
            </v-btn>
          </div>
        </v-card>

        <v-card class="pa-3 ma-3">
          <div class="d-flex flex-column">
            <span class="mb-3">Results: </span>
            <span>{{ results.message }}</span>
          </div>
        </v-card>

        <v-card class="pa-3 ma-3">
          <div class="d-flex flex-column">
            <span class="mb-3">Charge: </span>
            <span>P {{ results.charge }}</span>
          </div>
        </v-card>
      </div>
    </v-col>
  </v-row>
</template>

<script>
import moment from 'moment'
import { required } from 'vee-validate/dist/rules'
import { extend, ValidationObserver, ValidationProvider, setInteractionMode } from 'vee-validate'
import EntryPoint from '~/store-entities/models/EntryPoint'
import slotCard from '~/components/parking-slot'
import ParkingTransaction from '~/store-entities/models/ParkingTransaction'

setInteractionMode('eager')

extend('required', {
  ...required,
  message: '{_field_} cannot be empty'
})

export default {
  components: {
    slotCard,
    ValidationProvider,
    ValidationObserver
  },
  data () {
    return {
      vehicle: {
        plate_no: null,
        type: null
      },
      entry_point_id: null,
      vehicleTypes: [
        { id: 0, name: 'Small' },
        { id: 1, name: 'Medium' },
        { id: 2, name: 'Large' }
      ],
      reference: null,
      date: moment().format('YYYY-MM-DD'),
      time: moment().format('HH:mm:ss'),
      use_server_time: true,
      results: {
        message: null,
        charge: 0
      }
    }
  },
  computed: {
    entryPoints () {
      const data = EntryPoint.query().with('slots').get() || []
      return data
    },
    parkingTransaction () {
      const data = ParkingTransaction.query().with(['slot', 'vehicle']).orderBy('created_at', 'desc').first() || {}
      return data
    },
    chargedParkingTransaction () {
      const data = ParkingTransaction.query()
        .where('status', 'charged')
        .where('reference', this.reference)
        .with(['slot', 'vehicle'])
        .orderBy('created_at', 'desc')
        .first() || {}
      return data
    }
  },
  async mounted () {
    await this.$store.dispatch('entities/entryPoints/getList')
  },
  methods: {
    async parkVehicle () {
      if (!(await this.$refs.refParkObserver.validate())) {
        return
      }

      try {
        await this.$store.dispatch('entities/parkingTransactions/create', {
          vehicle: {
            plate_no: this.vehicle.plate_no,
            type: this.vehicle.type
          },
          entry_point_id: this.entry_point_id,
          date: this.date,
          time: this.time,
          use_server_time: this.use_server_time
        })

        if (this.parkingTransaction) {
          this.results.message = `Vehicle has been parked in slot: ${this.parkingTransaction.slot.slot_no} with reference no. ${this.parkingTransaction.reference}`
        }
      } catch (_error) {
        if (_error.status === 422) {
          this.$refs.refParkObserver.setErrors(_error.data.errors)
        }
      }
    },
    async unparkVehicle () {
      if (!(await this.$refs.refUnparkParkObserver.validate())) {
        return
      }
      try {
        await this.$store.dispatch('entities/parkingTransactions/update', {
          reference: this.reference,
          date: this.date,
          time: this.time,
          use_server_time: this.use_server_time
        })
        this.results.charge = this.chargedParkingTransaction.rate
      } catch (_error) {
        this.results.message = (_error.data) ? _error.data.message : null
      }
    },
    redirectTransactions () {
      this.$router.push('/parking-transactions')
    },
    updateDate () {
      this.date = moment().format('YYYY-MM-DD')
      this.time = moment().format('HH:mm:ss')
    }
  }
}
</script>
