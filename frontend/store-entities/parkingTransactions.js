export default {
  actions: {
    async create ({ dispatch }, data) {
      const responseData = await this.$axios.post('/api/transactions', data)
      dispatch(
        'entities/parkingTransactions/insertOrUpdate',
        { data: responseData.transaction },
        { root: true }
      )

      dispatch(
        'entities/slots/insertOrUpdate',
        { data: responseData.slot },
        { root: true }
      )
    },

    async getList ({ dispatch }) {
      const responseData = await this.$axios.get('api/transactions')
      console.log(responseData)
      dispatch(
        'entities/parkingTransactions/insertOrUpdate',
        { data: responseData },
        { root: true }
      )
    },

    async update ({ dispatch }, data) {
      const responseData = await this.$axios.put(`/api/transactions/${data.reference}`, data)
      console.log(responseData)
      dispatch(
        'entities/parkingTransactions/insertOrUpdate',
        { data: responseData.transaction },
        { root: true }
      )
    }

  }
}
