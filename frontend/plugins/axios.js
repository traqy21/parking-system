export default function (ctx) {
  ctx.$axios.onError(async (error) => {
    if (error.response) {
      if (error.response.status === 418) {
        ctx.app.store.dispatch('uiInactivityWarning/show')
      }
      if (error.response.status === 401 && ctx.app.$auth.state.loggedIn) {
        await ctx.app.store.dispatch('entities/deleteAll')
        await ctx.app.$auth.logout()
      }
      return Promise.reject(error.response)
    }
    return Promise.reject(error)
  })

  ctx.$axios.onRequest((config) => {
    // reject promise if browser is offline
    if (
      typeof window.navigator.onLine !== 'undefined' &&
      !window.navigator.onLine
    ) {
      return Promise.reject(Error('Navigator offline'))
    }
    // Rewrite BaseUrl to use subdomain
    config.baseURL = `${process.env.API_PROTOCOL}${process.env.API_HOST}`

    // Optionally include xdebug flag
    if (process.env.xdebug === 'true') {
      config.url += '?XDEBUG_SESSION_START=true'
    }
    config.headers.Accept = 'application/json'
    config.headers['Content-Type'] = 'application/json'
  })

  ctx.$axios.onResponse((response) => {
    // Checks header for auth token
    // If exists, this is a token refresh. Update the token in store with the new token
    if (Object.prototype.hasOwnProperty.call(response.headers, 'authorization')) {
      const authorization = response.headers.authorization
      const token =
        ctx.app.$auth.strategy.options.tokenType +
        ' ' +
        authorization.replace('Bearer ', '')
      // We need both setTokens here, the first one sets headers in axios requests
      // while the second one sets in memory & storage & cookie
      ctx.app.$auth.strategy._setToken(token)
      ctx.app.$auth.setToken(ctx.app.$auth.strategy.name, token)
    }
    // This is a workaround for using the @nuxtjs/auth library
    // For now, it does not parse nested response upon login to get the token
    if (!Object.prototype.hasOwnProperty.call(response.data, 'data')) {
      return response
    }

    if (
      Object.prototype.hasOwnProperty.call(response.data.data, 'access_token') ||
      Object.prototype.hasOwnProperty.call(response.data.data, 'me')
    ) {
      return response.data
    }

    return response.data.data
  })
}
