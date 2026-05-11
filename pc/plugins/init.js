import Cookies from 'js-cookie'
export default async function ({  store }, inject) {
  const token = Cookies.get('token')
  token && store.commit('setToken', token)
  await store.dispatch('getCategory')
  await store.dispatch('getPublicData')
  const getImageUri = (url = '') => {
    const oss_domain = store.getters.ossDomain || ''
    return oss_domain + url
  }
  inject("getImageUri", getImageUri);
}