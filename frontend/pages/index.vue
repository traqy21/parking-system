<template>
  <v-row>
    <v-col cols="8">
      <v-card class="ma-3 pa-3">
        <div class="d-flex flex-row">
          <v-card-title>PARKING COMPLEX</v-card-title>
          <v-btn
            color="primary"
            rounded
            x-large
            depressed
            class="text-none ma-2"
            @click="redirectTransactions()"
          >
            View transactions
          </v-btn>
        </div>

        <v-row v-for="(item, key) in entryPoints" :key="key">
          <v-col cols="12">
            <h3>{{ item.name }}</h3>
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
          <div class="d-flex flex-column pa-3">
            <h3 class="pb-2">
              Park vehicle:
            </h3>
            <v-text-field
              v-model="form.vehicle.plate_no"
              outlined
              placeholder="Plate No."
            />
            <v-select
              v-model="form.vehicle.type"
              :items="vehicleTypes"
              item-text="name"
              item-value="id"
              outlined
              label="Vehicle Type"
            >
              <template v-slot:selection="{ item }">
                <span class="text-md-left" style="width: 100%;">
                  {{ item.name }}
                </span>
              </template>
            </v-select>
            <v-select
              v-model="form.entry_point_id"
              :items="entryPoints"
              item-text="name"
              item-value="id"
              outlined
              label="Entry points"
            >
              <template v-slot:selection="{ item }">
                <span class="text-md-left" style="width: 100%;">
                  {{ item.name }}
                </span>
              </template>
            </v-select>
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
        </v-card>

        <v-card class="pa-3 ma-3">
          <div class="d-flex flex-column pa-3">
            <h3 class="pb-2">
              Unpark vehicle:
            </h3>
            <v-text-field
              v-model="reference"
              outlined
              placeholder="Reference ID"
            />
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
            <span>Results: </span>
          </div>
        </v-card>
      </div>
    </v-col>
  </v-row>
</template>

<script>
import EntryPoint from '~/store-entities/models/EntryPoint'
import slotCard from '~/components/parking-slot'
export default {
  components: {
    slotCard
  },
  data () {
    return {
      form: {
        vehicle: {
          plate_no: null,
          type: null
        },
        entry_point_id: null
      },
      vehicleTypes: [
        { id: 0, name: 'Small' },
        { id: 1, name: 'Medium' },
        { id: 2, name: 'Large' }
      ],
      reference: null
    }
  },
  computed: {
    entryPoints () {
      const data = EntryPoint.query().with('slots').get() || []
      return data
    }

  },
  async mounted () {
    await this.$store.dispatch('entities/entryPoints/getList')
    console.log(this.entryPoints)
  },
  methods: {
    async parkVehicle () {
      console.log(this.form)
      await this.$store.dispatch('entities/parkingTransactions/create', this.form)
    },
    async unparkVehicle () {
      console.log(this.reference)
      await this.$store.dispatch('entities/parkingTransactions/update', this.reference)
    },
    redirectTransactions () {
      this.$router.push('/parking-transactions')
    }
  }
}
</script>
