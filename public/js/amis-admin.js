window._ADMIN_AMIS_CONFIG = {
  akHeader: 'X-Api-Key',
  localKey: 'ADMIN-X-API-KEY',
  loginApi: '/admin/auth/login',
  loginUrl: '/admin/login',
  akResponseKey: 'access_token',
}
window.amisAppProps = {}
window.amisAppEnv = {
  requestAdaptor(api) {
    api.headers[_ADMIN_AMIS_CONFIG.akHeader] = localStorage.getItem(_ADMIN_AMIS_CONFIG.localKey)
    return api;
  },
  responseAdaptor(api, payload, query, request, response) {
    if (api.url === _ADMIN_AMIS_CONFIG.loginApi && payload.status === 0 && payload.data[_ADMIN_AMIS_CONFIG.akResponseKey]) {
      localStorage.setItem(_ADMIN_AMIS_CONFIG.localKey, payload.data[_ADMIN_AMIS_CONFIG.akResponseKey])
    }
    if (payload.status === 401) {
      window.location.href = _ADMIN_AMIS_CONFIG.loginUrl
    }
    if ([301, 302].indexOf(payload.status) !== -1 && payload.data.redirect) {
      if (payload.data.target === '_blank') {
        window.open(payload.data.redirect)
      } else {
        window.location.href = payload.data.redirect
      }
      payload.status = 0 // 改为正确响应，确保该接口提示的是成功的
    }
    return payload
  },
}
window.amisAppBeforeLoad = (amisLib) => {
  // amisRequire('amis') 之后，可以用于扩展语言
}
window.amisAppLoaded = (amisApp) => {
  // amis.embed 之后，可以控制 amisApp
}
