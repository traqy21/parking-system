<template>
  <v-row>
    <v-col cols="12">
      <v-card class="ma-3 pa-3">
        <div class="d-flex flex-row">
          <v-card-title>PARKING Transactions</v-card-title>
          <div class="d-flex flex-column pa-3">
            <v-btn
              color="primary"
              rounded
              x-large
              depressed
              class="text-none ma-2"
              @click="redirectParkingComplex()"
            >
              View Parking Complex
            </v-btn>
          </div>
        </div>
        <v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-left">
                  Reference
                </th>
                <th class="text-left">
                  Plate No.
                </th>
                <th class="text-left">
                  Slot No.
                </th>
                <th class="text-left">
                  Status
                </th>
                <th class="text-left">
                  Entry Time
                </th>
                <th class="text-left">
                  Exit Time
                </th>
                <th class="text-left">
                  Charged Rate
                </th>
                <th class="text-left">
                  Description
                </th>
                <th class="text-left">
                  Charge per hour
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="item in parkingTransactions"
                :key="item.name"
              >
                <td>{{ item.reference }}</td>
                <td>{{ item.vehicle.plate_no }}</td>
                <td>{{ item.slot.slot_no }}</td>
                <td>{{ item.status }}</td>
                <td>{{ item.created_at }}</td>
                <td>{{ item.exit_time }}</td>
                <td>{{ item.rate }}</td>
                <td>{{ item.description }}</td>
                <td>{{ item.slot.exceeding_hourly_rate }}</td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import ParkingTransaction from '~/store-entities/models/ParkingTransaction'
export default {
  data () {
    return {

    }
  },
  computed: {
    parkingTransactions () {
      const data = ParkingTransaction.query().with(['slot', 'vehicle']).orderBy('created_at', 'desc').get() || []
      return data
    }
  },
  async mounted () {
    await this.$store.dispatch('entities/parkingTransactions/getList')
    console.log(this.parkingTransactions)
  },
  methods: {
    redirectParkingComplex () {
      this.$router.push('/')
    }
  }
}
</script>
