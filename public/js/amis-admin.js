const AK_LOCAL_KEY = 'X-API-KEY';
const AMIS_CONFIG = {
  akHeader: 'X-Api-Key',
  localKey: 'ADMIN-X-API-KEY',
  loginApi: '/admin/auth/login',
  loginUrl: '/admin/login',
  akResponseKey: 'access_token',
}
window.amisAppProps = {}
window.amisAppEnv = {
  requestAdaptor(api) {
    api.headers[AMIS_CONFIG.akHeader] = localStorage.getItem(AMIS_CONFIG.localKey)
    return api;
  },
  responseAdaptor(api, payload, query, request, response) {
    if (api.url === AMIS_CONFIG.loginApi && payload.status === 0 && payload.data[AMIS_CONFIG.akResponseKey]) {
      localStorage.setItem(AMIS_CONFIG.localKey, payload.data[AMIS_CONFIG.akResponseKey])
    }
    if (payload.status === 401) {
      window.location.href = AMIS_CONFIG.loginApi
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
