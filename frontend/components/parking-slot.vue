<template>
  <v-card
    class="ma-2 pa-2"
    width="300"
    :color="(parkingSlot.is_vacant) ? 'green darken-2': 'red darken-1'"
  >
    <h2>No: {{ parkingSlot.slot_no }}</h2>

    <div class="d-flex flex-column">
      <span>Size: {{ readableSize(parkingSlot.size) }}</span>
      <span>Distance: {{ parkingSlot.distance }}</span>
    </div>
  </v-card>
</template>

<script>
export default {
  props: {
    parkingSlot: {
      type: Object,
      required: true
    }
  },
  data () {
    return {

    }
  },
  methods: {
    readableSize (value) {
      if (value === 0) {
        return 'Small'
      }

      if (value === 1) {
        return 'Medium'
      }

      if (value === 2) {
        return 'Large'
      }
    }
  }
}
</script>
