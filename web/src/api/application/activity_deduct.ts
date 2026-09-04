import request from '@/plugins/axios'
export const apiGetDeductConfig = (): Promise<any> =>
    request.get('activity.deduct_activity/getConfig')
export const apiSetDeductConfig = (params: any): Promise<any> =>
    request.post('activity.deduct_activity/setConfig', params)
