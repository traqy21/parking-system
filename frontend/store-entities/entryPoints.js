export default {
  actions: {
    async getList ({ dispatch }) {
      const responseData = await this.$axios.get('api/entry-points')
      console.log(responseData)
      dispatch(
        'entities/entryPoints/insertOrUpdate',
        { data: responseData },
        { root: true }
      )
    }
  }
}
